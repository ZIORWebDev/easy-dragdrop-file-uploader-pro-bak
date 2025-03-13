import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import heic2any from "heic2any";
import wpFilepond from "../filepond/helpers.js";

function registerFilePondPlugins(plugins) {
    plugins.push(FilePondPluginImagePreview);

    return plugins;
}

function isHEIFSupported() {
  const canvas = document.createElement("canvas");
  const dataURL = canvas.toDataURL("image/heif");

  return dataURL.startsWith("data:image/heif") && dataURL.length > 10; // Ensures valid output
}

wpFilepond.addFilter("wp_filepond_plugins", registerFilePondPlugins);

$(document).on("wp_filepond_instance_created", function (event, filePondInstance) {
    // Since some browsers don't support HEIC/HEIF images, errors may occur.
    // This workaround detects HEIC/HEIF file extensions and assigns the correct MIME type.
    filePondInstance.fileValidateTypeDetectType = async (file, type) => {
        return new Promise((resolve, reject) => {
            // If the file type is already set, return it
            if (type) {
                resolve(type);
            }

            // Check if the filename has a .heic or .heif extension
            const fileName = file.name.toLowerCase();
            const match = fileName.match(/\.(heic|heif)$/);

            if (match) {
                const mimeType = `image/${match[1]}`;
                resolve(mimeType);
            }

            reject();
        });
    };

    // If preview is enable, convert HEIC/HEIF to JPEG to be able to preview the image since some browsers don't support HEIC/HEIF.
    filePondInstance.beforeAddFile = async (file) => {
        // If preview is not enabled, return true to allow the file to be added to FilePond immediately.
        if (!filePondInstance.allowImagePreview) {
            return file;
        }

        // If the browser support HEIF/HEIC, return true to allow the file to be added to FilePond immediately.
        if (isHEIFSupported()) {
            return file;
        }

        const fileName = file.file.name.toLowerCase();
        const match = fileName.match(/\.(heic|heif)$/);

        // If not a HEIC/HEIF file, return true to allow the file to be added to FilePond immediately.
        if (!match) {
            return file;
        }

        try {
            // Convert heic/heif to jpeg so that it can be previewed
            const convertedBlob = await heic2any({
                blob: file.file,
                toType: "image/jpeg",
                quality: 0.8, // Adjust quality
            });

            // Create new jpeg image file
            const convertedFile = new File([convertedBlob], file.file.name.replace(/\.heic$/, ".jpg"), {
                type: "image/jpeg"
            });

            file.setFile(convertedFile);

            return file;
        } catch (error) {
            console.error("HEIC to JPG conversion failed:", error);
        }
    };
});