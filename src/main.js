import createFilePondInstance from "./filepond/main.js";

$(document).ready(function () {
    const filePondInstances = new Map();
    const fileUploaderFields = $(".wp-filepond-upload");
    let filePondIntegration = FilePondUploader || {};

    filePondIntegration.allowMultiple = FilePondUploader.allowMultiple === "1";

    fileUploaderFields.each(function () {
        const configuration = Object.assign({}, getConfiguration($(this)), filePondIntegration);
        const filePondInstance = createFilePondInstance($(this)[0], configuration);

        // Raised event a FilePond instance is created
        $(document).trigger("wp_filepond_instance_created", filePondInstance);

        filePondInstances.set(this, filePondInstance);
    });

    // On elementor form success, clear the filepond field
    $(document).on("submit_success", function (event, response) {
        filePondInstances.forEach((instance) => instance.removeFiles());
    });
});

function getConfiguration(fileInput) {
    const data = $(fileInput).data();

    return {
        acceptedFileTypes: data.filetypes?.split(",") ?? null,
        allowMultiple: fileInput.attr("multiple") !== undefined,
        labelIdle: data.label ?? "",
        maxFileSize: data.filesize ? `${data.filesize}MB` : null,
        maxFiles: data.maxfiles ?? null
    }
}