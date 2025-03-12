import { convertHeif } from 'libheif-web';
import "./main.js";

$(document).on("wp_filepond_instance_created", function(event, filePondInstance) {
    console.log("FilePond instance created 1:", filePondInstance);

    // Convert HEIC/HEIF to JPEG before adding to FilePond
    filePondInstance.on("addfile", async function (error, file) {
        // Check if the file type is missing and if it's a .heic or .heif file
        const fileName = file.file.name.toLowerCase();
        const match = fileName.match(/\.(heic|heif)$/);

        if (file.fileType || !match) {
            return;
        }

        const mimeType = `image/${match[1]}`; // Dynamically assigns image/heic or image/heif

        // Create a new file object with the correct MIME type
        const correctedFile = new File([file.file], file.file.name, {
            type: mimeType,
            lastModified: file.file.lastModified
        });

        // Replace the original file with the corrected file
        filePondInstance.removeFile(file.id)
        // filePondInstance.addFile(correctedFile);
        filePondInstance.addFile(correctedFile, {
            type: "local", // This prevents FilePond from processing the file
        });

        try {
            const convertedFile = await convertHeif(
                file.file,
                file.file.name.replace(/\.(heic|heif)$/i, ".jpg"),
                "image/jpeg"
            );

            filePondInstance.removeFile(correctedFile); // Remove original HEIC file
            filePondInstance.addFile(convertedFile); // Add JPEG version
        } catch (error) {
            console.error("HEIC to JPEG conversion error:", error);
        }
    });

    return filePondInstance;
});