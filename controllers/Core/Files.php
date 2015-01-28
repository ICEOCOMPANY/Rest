<?php
/**
 * Kontroler uploadu plikow
 *
 * Created by PhpStorm.
 * Author: dawid
 * Date: 27.01.15
 * Time: 12:17
 */

namespace Controllers\Core;


class Files extends \Base\Controller
{

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PUBLICZNE


    /**
     * POST
     * Wysyla plik na serwer
     *
     * @return \Helpers\Response
     */
    public function upload()
    {

        $logged_user = (new \Controllers\Core\Auth())->getCurrentUserId();

        // Sprawdzam, czy zalogowany
        if (!$logged_user) {

            // Niezalogowany - zwracam blad
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(\Helpers\Messages::notLoggedError));
        } else {

            // Sprawdzam, czy sa jakies pliki
            if (!$this->request->hasFiles()) {
                $this->response
                    ->setCode(409)
                    ->setJsonErrors(array(\Helpers\Messages::noFilesToUploadError));
            } else {

                foreach ($this->request->getUploadedFiles() as $uploaded_file) {

                    // Ustawiam nazwe na unikalna
                    $filename = $uploaded_file->getName();
                    $directory_id = 0;
                    $filename = $this->uniqueFilename($logged_user, $filename, $directory_id);

                    // Tworze model pliku w bazie
                    $file = new \Models\Core\Files();
                    $file->setUserId($logged_user);
                    $file->setOriginalName($filename);
                    $file->setDirectoryId($directory_id);
                    $file->setTempName(\Models\Core\Files::generateTemporaryName($logged_user, $filename, $directory_id));
                    $file->setSize($uploaded_file->getSize());
                    $file->setType($uploaded_file->getType());
                    $file->setCreationTime((new \DateTime())
                        ->format(\Helpers\Consts::mysqlDateTimeColumnFormat));
                    $file->setModificationTime((new \DateTime())
                        ->format(\Helpers\Consts::mysqlDateTimeColumnFormat));

                    if($this->request->get('public'))
                        $file->setPublic($this->request->get('public'));
                    else
                        $file->setPublic(0);


                    if (!$file->save()) {

                        // Nie udalo sie zapisac - zwracam bad
                        $this->response
                            ->setCode(409)
                            ->setJsonErrors(array(\Helpers\Messages::couldNotSaveFile));
                    } else {

                        // Udalo sie zapisac

                        // Przenosze plik do katalogu storage
                        $uploaded_file->moveTo('../storage/' . $file->getTempName());

                        // Zwracam informacje o pliku
                        $this->response
                            ->setJson($this->getData($file));
                    }
                }
            }
        }
        return $this->response;
    }

    /**
     * GET
     * Pobieranie pliku
     *
     * @param $id - id pliku / hash z temp_name
     * @return \Helpers\Response
     */
    public function download($id)
    {
        $file = $this->get($id);

        if($file){
            self::readFile('../storage/' . $file->getTempName(), $file->getOriginalName());
        }

        return $this->response;
    }

    /**
     * GET
     * Pobieranie informacji o pliku
     *
     * @param $id - id pliku / hash z temp_name
     * @return \Helpers\Response
     */
    public function info($id)
    {
        $file = $this->get($id);

        if($file){
            $this->response->setJson($this->getData($file));
        }

        return $this->response;
    }




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PRYWATNE

    /**
     * Zwraca tablice z parametrami, ktore moze zobaczyc uzytkownik
     *
     * @param $file - obiekt pliku
     * @return array
     */
    private function getData($file){
        return array(
            "id" => $file->getId(),
            "user_id" => $file->getUserId(),
            "original_name" => $file->getOriginalName(),
            "directory_id" => $file->getDirectoryId(),
            "temp_name" => $file->getTempName(),
            "size" => $file->getSize(),
            "type" => $file->getType(),
            "public" => $file->getPublic(),
            "creation_time" => $file->getCreationTime(),
            "modification_time" => $file->getModificationTime(),
            "download_url" => $_SERVER['SERVER_NAME'] . "/files/" . $file->getTempName() . "/download"
        );
    }

    /**
     * Pobiera obiekt pliku
     *
     * @param $id - id pliku / hash z temp_name
     * @return bool|\Phalcon\Mvc\Model
     */
    private function get($id){
        $can_open = false;

        $file = \Models\Core\Files::findFirst(array(
            "id = :id: OR temp_name = :temp_name:",
            "bind" => array(
                "id" => $id,
                "temp_name" => $id
            )
        ));

        if (!$file) {
            // Nie znalazlo pliku - zwracam blad
            $this->response
                ->setCode(404)
                ->setJsonErrors(array(\Helpers\Messages::fileNotFoundError));
        } else {


            // Czy plik jest publiczny?
            if ($file->getPublic()) {

                // Jest publiczny - pobieram
                $can_open = true;
            } else {
                // Nie jest publiczny
                $logged_user = (new \Controllers\Core\Auth())->getCurrentUserId();

                if (!$logged_user) {

                    // Niezalogowany - zwracam blad
                    $this->response
                        ->setCode(401)
                        ->setJsonErrors(array(\Helpers\Messages::notLoggedError));
                } else {

                    // Zalogowany - sprawdzam, czy jest wlascicielem
                    if ($file->getUserId() != $logged_user) {

                        // Nie jest wlescicielem - zwracam blad
                        $this->response
                            ->setCode(401)
                            ->setJsonErrors(array(\Helpers\Messages::noPermissionsToDownloadFileError));

                    } else {

                        // Jest wlascicielem pliku - pobieram
                        $can_open = true;
                    }

                }
            }
        }

        if($can_open)
            return $file;

        return false;
    }

    /**
     * Tworzy unikalna nazwe pliku w katalogu
     *
     * @param $user_id - id wlasciciela pliku
     * @param $name - nazwa pliku
     * @param $directory_id - id katalogu
     * @return string
     */
    private function uniqueFilename($user_id, $name, $directory_id)
    {

        $i = 0;
        $exists = true;

        $name = array(
            "nam" => pathinfo($name, PATHINFO_FILENAME),
            "ver" => "(" . $i . ")",
            "ext" => "." . pathinfo($name, PATHINFO_EXTENSION),
        );

        while ($exists) {

            if ($i == 0)
                $name["ver"] = "";
            else
                $name["ver"] = "(" . $i . ")";

            $exists = \Models\Core\Files::findFirst(array(
                "user_id = :user_id: AND original_name = :original_name: AND directory_id = :directory_id:",
                "bind" => array(
                    "user_id" => $user_id,
                    "original_name" => implode("", $name),
                    "directory_id" => $directory_id
                )
            ));

            $i++;
        }

        return implode("", $name);
    }

    /**
     * Czyta plik binarnie
     *
     * @param $location - sciezka do pliku
     * @param $filename - nowa nazwa pliku
     * @param string $mimeType - typ mime (opcjonalnie)
     */
    private static function readFile($location, $filename, $mimeType = 'application/octet-stream')
    {
        $size = filesize($location);
        $time = date('r', filemtime($location));

        $fm = @fopen($location, 'rb');
        if (!$fm) {
            header("HTTP/1.0 505 Internal server error");
            return;
        }

        $begin = 0;
        $end = $size;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[0]);
                if (!empty($matches[1]))
                    $end = intval($matches[1]);
            }
        }

        if ($begin > 0 || $end < $size)
            header('HTTP/1.0 206 Partial Content');
        else
            header('HTTP/1.0 200 OK');

        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . ($end - $begin));
        header("Content-Range: bytes $begin-$end/$size");
        header("Content-Disposition: inline; filename=$filename");
        header("Content-Transfer-Encoding: binary\n");
        header("Last-Modified: $time");
        header('Connection: close');

        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && $cur < $end && (connection_status() == 0)) {
            print fread($fm, min(1024 * 16, $end - $cur));
            $cur += 1024 * 16;
        }
    }

} 