import FileUploader from "../ds/file_uploader.js";

$("#ID_UPLOAD").on('click', () => {
    $('#ID_FILE_UPLOADER').trigger('click');
});

$("#ID_FILE_UPLOADER").on('change', async (e) => {

    let files = Object.values(e.target.files);

    let uploader = new FileUploader(files);

    const session_initialized = await uploader.initUploadSession();
    
    if (session_initialized)    
    {
        uploader.upload();
    }
})


/*$("#ID_FILE_UPLOADER").on('change', (e) => {

    var files = Object.values(e.target.files)
    var formData = new FormData();

    for (var i = 0; i < files.length; i++) {
        formData.append(i, files[i]);
    }

    $.ajax({
        
        url: '/clouddrive/upload',
        data: formData,
        type: "POST",
        processData: false,  
        contentType: false,  
        
        success: (response) => {
            console.log(response);
        },
        error: (xhr) => {
            console.log(xhr);
        }
    }); 
});*/

const download_recoverykey = () => {
    
    download_file(RECOVERY_KEY_FILENAME, RECOVERY_KEY);

    return;

    $.ajax({        
        method: 'GET',
        url: URL,
        dataType: 'json',
        data: {
            recoverykey: true,
        },

        success: function(response) {

            download_file(RECOVERY_KEY_FILENAME, response.recoverykey)
        },

        error: function(xhr, status, error) {

            errorMsg = ""

            try {
                errorMsg = xhr.responseJSON.status_message
            }
            catch (e)
            {
                errorMsg = "There was a problem, try again";
            }   
            
            alert(errorMsg)
        }
    })
}

const download_file = (filename, fileCtx) => {

    // Creazione di un oggetto Blob contenente il testo
    var blob = new Blob([fileCtx], { type: "text/plain" });
    
    // Creazione di un URL del blob
    var url = window.URL.createObjectURL(blob);

    // Creazione di un elemento "a" per il download
    var a = document.createElement("a");
    a.href = url;
    a.download = filename; // Nome del file

    // Aggiunta dell'elemento "a" al documento
    document.body.appendChild(a);

    // Simulazione del clic sull'elemento "a" per avviare il download
    a.click();

    // Rimozione dell'elemento "a" dopo il download
    document.body.removeChild(a);

    // Revoca l'url
    window.URL.revokeObjectURL(url);
}

$('#rkeyDownloadButton').on('click', () => {
    download_recoverykey();
});