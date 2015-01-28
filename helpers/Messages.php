<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 23.01.15
 * Time: 13:15
 */

namespace Helpers;


class Messages {
    const unknownError = "Unknown error";
    const notLoggedError = "You are not logged. To do anything, you must login first.";
    const noPermissionsToEditError = "You have no permissions to edit this data.";
    const tooManyRequestsError = "Too many requests. Try to request again after a while.";
    const passwordResetKeyNotFoundError = "Password reset key not found or is expired. Try to generate new.";
    const userNotFoundError = "User fulfilling the criteria not found.";
    const passwordRequirementsNotFulfilledError = "Password requirements not fulfilled. Password must contains at least 8 characters.";
    const noFilesToUploadError = "There are no files to upload.";
    const couldNotSaveFile = "Could not save file.";
    const fileNotFoundError = "File not found.";
    const noPermissionsToDownloadFileError = "You have no permissions to download file.";
}