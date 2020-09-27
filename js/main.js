
sleep = (ms) => {
	return new Promise((resolve) => setTimeout(resolve, ms))
}

recursiveRenewStatus = async () => {
    let waitingTasks = await document.querySelectorAll('[data-status="wait"]')
    if (waitingTasks.length > 0) {
        for (let i = 0; i < waitingTasks.length; i++) {
            let waitingTask = waitingTasks[i]
            if (!waitingTask.dataset.task) { 
                continue 
            }
            let url = `task?task_id=${waitingTask.dataset.task}`
            let response = await fetch(url, { method: "GET" })
            response = await response.json()
            pasteNewTask(
                waitingTask.dataset.id,
                response.status,
                response.retry_id,
                waitingTask.dataset.name
            )
            await console.log(response)
            await sleep(2000)
        }
    } 
    await sleep(2000)
    recursiveRenewStatus()
}
recursiveRenewStatus()

pasteNewTask = (id, status, retry_id, name, result) => {
    htmlTemplate = `<div class="task-item" data-id="${id}" data-status="${status}" data-task="${retry_id}" data-name="${name}">
                    <p>
                        <strong>Имя:</strong> ${name} 
                        <strong>Результат:</strong> ${result} 
                    </p>
                </div>`
    let existTask = document.querySelector('[data-id="' + id + '"]')
    if (existTask) {
        existTask.outerHTML = htmlTemplate
    } else {
        let tasksDiv = document.querySelector(".tasks")
        tasksDiv.innerHTML = htmlTemplate + tasksDiv.innerHTML
    }                
}

let uploadPhotoForm = document.getElementById("upload-photo-form")
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
    nameInput.value = ""
    photoInput.value = ""
    let response = await fetch("/upload-photo", {
        method: "POST",
        body: new FormData(uploadPhotoForm),
    })
    response = await response.json()
    if (response.status == 'failed') {
        alert('Ошибка сервера')
        return;
    }
    await pasteNewTask(
		response.id,
		response.status,
		response.retry_id,
		nameInput.value,
		response.result
    )
}
