
let uploadPhotoForm = document.getElementById("upload-photo-form")
if (uploadPhotoForm.length > 0) 
{
    uploadPhotoForm.onsubmit = async (e) => {
        e.preventDefault()
        let errorsHolder = uploadPhotoForm.querySelector(".errors")
        errorsHolder.innerHTML = ""
        let nameInput = uploadPhotoForm.querySelector("#name")
        let photoInput = uploadPhotoForm.querySelector("#photo")
        let errors = []
        if (!nameInput.value) {
            errors.push('Пустое имя')
        }
        if (!photoInput.value) {
            errors.push("Пустое фото")
        }
        if (errors.length > 0) 
        {
            errors = errors.join("<br>")
            uploadPhotoForm.querySelector(".errors").innerHTML = errors
            return
        }
        let response = await fetch("/upload-photo", {
            method: "POST",
            body: new FormData(uploadPhotoForm),
        })
        response = await response.json();
        await console.log(response)

    }

}