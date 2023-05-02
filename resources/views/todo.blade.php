<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>todo.app</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
    <div>  
        <nav class="navbar bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Панель навигации</a>
            </div>
        </nav>

        <div class="container">

        <div class="input-group mb-3 mt-3">
            <input type="text" class="form-control" placeholder="" aria-label="" id="search-input" aria-describedby="button-addon2">
            <button class="btn btn-outline-primary" type="button" id="button-addon2">Search</button>
        </div>

        <div class="list-group mt-4 mb-4">
        </div>        

        <!-- Кнопка-триггер модального окна -->
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="refreshForm()">
            Add Task
        </button>

        <!-- Модальное окно -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                  
               <form name="add-task-form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="Input-title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="Input-title">
                    </div>
                    <div class="mb-3">
                        <label for="input-description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="input-description" cols="10" rows="5"></textarea>
                    </div> 
                    <div class="mb-3" id="image-block">
                        <img src="" class="img-thumbnail">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteimage" value="true" id="image-checkbox">
                            <label class="form-check-label" for="image-checkbox">
                                delete image
                            </label>
                        </div>
                    </div>
                    <div class="mb-3" id="input-image-block">                       
                        <label for="input-image" class="form-label">Image</label>
                        <input class="form-control" type="file" name="image" id="input-image">
                    </div>  
                    <div class="mb-3" id="tags-block">
                        <label for="Input-tag" class="form-label">Tags</label>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tag" id="Input-tag">
                            </div>
                            <div class="col-sm-4"> 
                                <button type="button" class="btn btn-primary" onclick="addTag()"> Add Tag </button>
                            </div>                                                 
                        </div>
                        <div id="tags-blok">

                        </div>

                    </div>                   
                    <input type="hidden" name="id">
                </form>               

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-button">Create</button>
                <button type="button" class="btn btn-primary" id="red-button">Save</button>
            </div>
            </div>
        </div>
        </div>

        </div>
    </div>
    <script>

        const addButton = document.getElementById("add-button");
        const redButton = document.getElementById("red-button");
        const searchInput = document.getElementById("search-input");

        addButton.addEventListener("click", addTask);
        redButton.addEventListener("click", redTask);
        searchInput.addEventListener("keyup", search);


        async function addTag(){

            const form = document.forms['add-task-form']

            const formData = new FormData()

            formData.append("task_id", form.id.value);
            formData.append("tag", form.tag.value);

            let response = await fetch('/api/tag' , {

                method: 'POST',
                body: formData
            });            

            let result = await response.json()

            form.tag.value = null

            console.log(result)
        }       

        async function redact(id){

            const addButton = document.getElementById("add-button");
            const redButton = document.getElementById("red-button");              
            const tagsBlock = document.getElementById('tags-block') 

            addButton.style.display = 'none'
            redButton.style.display = 'block'
            tagsBlock.style.display = 'block'

            let response = await fetch('/api/task/' + id, {

                method: 'GET',

            });

            let result = await response.json()

            await updateRedactForm(result)

        }

        async function updateRedactForm(result){

            const inputImageBlock = document.getElementById('input-image-block')
            const imageBlock = document.getElementById('image-block')
            const form = document.forms['add-task-form']

            form.title.value = result.data.title
            form.description.value = result.data.description
            form.id.value = result.data.id            

            if( result.data.image !== null ){
                
                imageBlock.firstElementChild.src = `/storage/image/preview/` + result.data.image
                inputImageBlock.style.display = 'none'
                imageBlock.style.display = 'block'
                form.image.value = null
                            
            }else{
                
                inputImageBlock.style.display = 'block'
                imageBlock.style.display = 'none'
            }

            tagRender(result.data.tags)

            loadTask()

        }

        async function tagRender(tags){
         
            const tagsBlok = document.getElementById('tags-blok')

            tagsBlok.innerHTML = ''
           
            for( let i in tags ){

                let element = document.createElement("span")

                element.classList.add('badge', 'text-bg-light', 'p-2', 'm-2')
                element.innerText = tags[i].tag
             
                tagsBlok.appendChild(element);                  
            }
        }

        async function redTask(){

            const form = document.forms['add-task-form']
               
            const formData = new FormData(form)

            let response = await fetch('/api/task/update' , {

                method: 'POST',
                body: formData
            });

            let result = await response.json();

            await updateRedactForm(result)
                    
        }

        async function addTask(){

            const form = document.forms['add-task-form']
   
            const formData = new FormData(form)

            
            let response = await fetch('/api/task', {

                method: 'POST',
                body: formData
            });

            let result = await response.json();

            loadTask()
            
        }

        async function loadTask(){

            let response = await fetch('/api/task', {

                method: 'GET'
            });

            let result = await response.json();

            taskRender(result)

        }

        async function taskRender(result){

            const listGroup = document.getElementsByClassName("list-group")[0];

            listGroup.innerHTML = '';

            for(let task in result.data){

                const element = document.createElement("div");
              
                element.classList.add('list-group-item', 'list-group-item-action');
                element.innerHTML = createElement(result.data[task])
          
                listGroup.appendChild(element);            
            }

        }

        function createElement(data){

            let str = ''

            str += `<div class="d-flex w-100 justify-content-between">`
            str +=  `<div>`
            str +=       `<h5 class="mb-1">` + data.title + `</h5>` 
            str +=       `<p class="mb-1">` + data.description + `</p>`

            str +=       `<small class="text-muted">`  + data.created_at + `</small>`
           
            str +=       `<div>`

            for( let i in data.tags){

                str +=       `<span class="badge text-bg-light p-2 m-2">` + data.tags[i].tag + `</span>`
            }

            str +=       `</div>` 

            str +=       `<div>`
            str +=             `<button class="btn btn-sm btn-light m-2" onclick="redact(` + data.id + `)" data-bs-toggle="modal" data-bs-target="#exampleModal" >redact</button>`
            str +=             `<button class="btn btn-sm btn-outline-danger m-2"  onclick="deleteTask(` + data.id + `)" >delete</button>` 
            str +=       `</div>`            

            str +=  `</div>`   
            str +=        data.image !== null ? `<div> <img src="/storage/image/preview/` + data.image + `" alt="..." class="img-thumbnail"> </div>` : '' ;
            str +=  `</div>`
                   
            return str
            
        }

        async function deleteTask(id){

          
            if(confirm('Delete')){

                let response = await fetch('/api/task/' + id, {

                    method: 'DELETE'
                }) 

                let result = await response.json();

                loadTask()
           
            }
        }

        function refreshForm(){

            const form = document.forms['add-task-form']

            const inputImageBlock = document.getElementById('input-image-block')
            const imageBlock = document.getElementById('image-block')
            const tagsBlock = document.getElementById('tags-block')  
            const addButton = document.getElementById("add-button")
            const redButton = document.getElementById("red-button")                     

            form.title.value = null
            form.description.value = null
            form.id.value = null
            form.image.value = null  
             
            inputImageBlock.style.display = 'block'
            imageBlock.style.display = 'none'
            tagsBlock.style.display = 'none'

            addButton.style.display = 'block'
            redButton.style.display = 'none'

        }

        async function search(e){

            if( e.target.value.length > 0 ){

                let response = await fetch('/api/search?key=' + e.target.value, {

                    method: 'GET',
                });

                let result = await response.json()

                taskRender(result)
             
            }else{
               
                loadTask()
            }

        }

        loadTask()
          
    </script>
</body>
</html>