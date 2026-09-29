  
  document.addEventListener('DOMContentLoaded', function() {
    
   const selectAll = document.getElementById('select-all');
   const tbody = document.querySelector('tbody');
   const deleteBtn = document.getElementById('delete-btn');
   const addSubjectBtn = document.getElementById('addSubject');

   const addSubjectModal = new bootstrap.Modal(document.getElementById('addSubjectModal'));
//    const showCourseModal = new bootstrap.Modal(document.getElementById('showCourseModal'));
//    const editCourseModal =  new bootstrap.Modal(document.getElementById('editCourseModal'));



    let currentOrder = 'desc';
    let currentLimit = 10;
    let currentPage = 1;


    getData(currentOrder, currentLimit, currentPage);


    document.getElementById("order").addEventListener('change', (e) => {

        currentOrder = e.target.value;
        currentPage = 1;
        getData(currentOrder, currentLimit,currentPage);

    });


    
    document.getElementById("limit").addEventListener('change', (e) => {


        currentLimit = e.target.value;
        currentPage = 1; 

        getData(currentOrder, currentLimit, currentPage);

    });


    document.getElementById("search").addEventListener("input", function(e) {

    const order = document.getElementById("order").value;
    const limit = document.getElementById("limit").value;
    
    getData(order, limit, 1); 

    });



    document.getElementById('pdf').addEventListener('click',()=>{

        const order = document.getElementById("order").value;
         getPdf(order);

    });

    document.getElementById('excel').addEventListener('click',()=>{

       const order = document.getElementById("order").value;
       getExcel(order);

    });

    document.getElementById('csv').addEventListener('click',()=>{

       const order = document.getElementById("order").value;
       getCsv(order);

    });



      /*  ========================================================================================= 
       |                                                                                         |
       |     Buttons                                                                             |
       |                                                                                         |
       ========================================================================================= 
    */

    
    // show and hide the delete button when there is checked box.

    function updateDeleteButton() {
        const checkedCount = tbody.querySelectorAll('.activity-checkbox:checked').length;
        deleteBtn.classList.toggle('d-none', checkedCount === 0);
    }

    // reset the checkbox and button delete
    function resetAll()
    {
        selectAll.checked =false;
        deleteBtn.classList.add('d-none');    
    }



    tbody.addEventListener('change', function(e) {
        if(e.target.classList.contains('activity-checkbox')) {
            updateDeleteButton();
        }
    });

    selectAll.addEventListener('change', function() {

        const allChildren = tbody.querySelectorAll('.activity-checkbox');

        allChildren.forEach(checkbox => {
            checkbox.checked = this.checked;
        });

        updateDeleteButton(); 
    });



    deleteBtn.addEventListener('click', function(){

        const ids = Array.from(
            tbody.querySelectorAll('.activity-checkbox:checked')
        ).map(cb => cb.value);

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: `Yes, delete (${ids.length}) item/s!`,
            }).then((result) => {
            if (result.isConfirmed) {

                fetch(`${BASE_URL}/subject/delete`, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ ids })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){

                        Swal.fire({
                            title: "Deleted!",
                            text: "Item/s has been deleted.",
                            icon: "success"
                            });

                        resetAll();
                        getData(currentOrder, currentLimit, currentPage);
                    } else {
                        alert('Delete failed!');
                    }

                    

                });
                
            }
            });

        });

     /*  ========================================================================================= 
       |                                                                                         |
       |     Add Modal                                                                           |
       |                                                                                         |
       ========================================================================================= 
    */
    

    // show  

    addSubjectBtn.addEventListener('click',function(){

        addSubjectModal.show();
    
    });


    // submit button connect to form

    document.getElementById('addSubjectSubmit').addEventListener('click', function() {

    document.getElementById('subjectForm').requestSubmit();
       
    });


    // close button with reset

    document.getElementById('closeBtn').addEventListener('click',function(){

    resetForm('subjectForm');

    });

    // ai input cleaner text-wrapper    

    const aiInputClean = document.querySelectorAll('.ai-clean');
    const aiToggleEnabler  = document.getElementById('aiAutoCorrect');
    
    aiInputClean.forEach(input => {


        input.addEventListener('blur', function(){

        if (!aiToggleEnabler.checked) return;

        const originalText = this.value.trim();

        if (originalText.length < 4) return;


        fetch(`${BASE_URL}/cleaner`, { 
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: originalText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.cleaned) {
                this.value = data.cleaned;
            }
        })
        .catch(err => {
            console.error('AI Cleanup Error:', err);
        });

    });



    });

   
    // form action

    document.getElementById('subjectForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

        const formData = new FormData(this);

        fetch(this.action, { 
            method: 'POST',    
            body: formData,
        })
        .then(res => res.json())
        .then(data => {

               
        document.querySelectorAll('.error').forEach(el => el.innerText = '');

        document.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('is-invalid');
        });


        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

        if (data.status === 'error') {
            for (let field in data.errors) {

                const input = document.getElementById(field);
                const feedback = document.getElementById('error-' + field);
                
                input.classList.add('is-invalid');        
                feedback.innerText = data.errors[field]; 
            }

        } else if (data.status === 'success') {

            const form = document.getElementById('subjectForm'); 
            
            form.reset();
            document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

            getData(currentOrder, currentLimit, currentPage);

            addSubjectModal.hide();

            Swal.fire({
                title: "Success!",
                text: data.message,
                icon: "success"
             });

        }

        })
        .catch(err => console.log(err));
    });


      // ---- logic for view modal -----

    // show the view modal

    document.getElementById("studentsTableBody").addEventListener("click", function(e) {

    if (e.target.classList.contains("view-btn")) {

        const studentId = e.target.dataset.id;

        fetch(`${BASE_URL}/course/${studentId}`)
        .then(response => response.json())
         .then(result => {
          
            document.getElementById('showModalTitle').textContent = result.code;
            document.getElementById('show_course_code').value = result.code;
            document.getElementById('show_course_year').value = result.years;
            document.getElementById('show_course_name').textContent = result.name;

             showCourseModal.show();


         });

    }
    });


    // ---- logic for edit and update modal -----

    document.getElementById("studentsTableBody").addEventListener("click", function(e) {

         if (e.target.classList.contains("edit-btn")) {

            const studentId = e.target.dataset.id;

             fetch(`${BASE_URL}/course/${studentId}/edit`)
            .then(response => response.json())
            .then(result => {
            
            // prepare the form action with the id 

            let form = document.getElementById('editCourseForm');
            form.action = `${BASE_URL}/course/${studentId}/update`;
            
            document.getElementById('edit_course_code').value = result.code;
            document.getElementById('edit_course_year').value = result.years;
            document.getElementById('edit_course_name').value = result.name;

            editCourseModal.show();

         });

         }
    });

    // editCourseSubmit


    // document.getElementById('editCourseSubmit').addEventListener('click', function() {

    // document.getElementById('editCourseForm').requestSubmit();
       
    // });

    // reset the edit modal form when close 

    document.getElementById('editCloseBtn').addEventListener('click',function(){

    resetForm('editCourseForm');

    });


    // edit form action

    // document.getElementById('editCourseForm').addEventListener('submit', function(e) {
    //    e.preventDefault(); 

    //     const formData = new FormData(this);

    //     fetch(this.action, { 
    //         method: 'POST',    
    //         body: formData,
    //     })
    //     .then(res => res.json())
    //     .then(data => {
               
    //     document.querySelectorAll('.error').forEach(el => el.innerText = '');

    //     document.querySelectorAll('.form-control').forEach(input => {
    //         input.classList.remove('is-invalid');
    //     });


    //     document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

    //     if (data.status === 'error') {
    //         for (let field in data.errors) {

    //             const input = document.getElementById(field);
    //             const feedback = document.getElementById('error-' + field);
                
    //             input.classList.add('is-invalid');        
    //             feedback.innerText = data.errors[field]; 

    //         }

           

    //     } else if (data.status === 'success') {

    //         const form = document.getElementById('editCourseForm'); 
            
    //         form.reset();
    //         document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
    //         document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

    //         getData(currentOrder, currentLimit, currentPage);

    //         editCourseModal.hide();

    //         Swal.fire({
    //             title: "Success!",
    //             text: data.message,
    //             icon: "success"
    //          });
    //     }

    //     })
    //     .catch(err => console.log(err));
    // });



    });


     /*  ========================================================================================= 
       |                                                                                         |
       |     End of DOM Content                                                                  |
       |                                                                                         |
       ========================================================================================= 
    */



  


    function resetForm(formId)
    {

        const form = document.getElementById(formId); 

        form.reset(); 
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));


    }


    // pdf data 

    function getPdf(order)
    {
        const search = document.getElementById("search").value;

        //  fetch(`${BASE_URL}/students/pdf?status=${status}&search=${encodeURIComponent(search)}`)
        // .then(response => response.blob())
        // .then(blob => {
        //     const url = URL.createObjectURL(blob);
        //     window.open(url); 
        // })
        // .catch(err => console.error(err));
        

        // take this if you dont want redirection confirmation 

        window.open(
        `${BASE_URL}/course/pdf?order=${order}&search=${encodeURIComponent(search)}`,
        "_blank"
        );
    }


    function getExcel(order)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/course/excel?range=${order}&search=${encodeURIComponent(search)}`;
    }


    function getCsv(order)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/course/csv?order=${order}&search=${encodeURIComponent(search)}`;
    }




    // get data from students 

    function getData(order, limit, page = 1){

    const search = document.getElementById("search").value;
    const tbody = document.getElementById("studentsTableBody");

    fetch(`${BASE_URL}/subject/all?order=${order}&limit=${limit}&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(result => {

            tbody.innerHTML = "";

            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">No result found</td></tr>`;
                return;
            }

            result.data.forEach((subject,index) => {
                tbody.innerHTML += `
                    <tr class="activity-row">

                     <td><input type="checkbox" class="activity-checkbox" value="${subject.id}"></td>
                        <td>${index + 1}</td>
                        <td>${subject.code}</td>
                        <td>${subject.name} </td>
                        <td>${subject.units}</td>
                        <td>${subject.lecture_hours}</td>
                        <td>${subject.lab_hours}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary view-btn" data-id="${subject.id}">
                                View
                            </button>
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${subject.id}">
                                Edit
                            </button>
                        </td>
                        
                    </tr>
                `;
            });

            const rows = tbody.querySelectorAll(".activity-row");

            if (rows.length > 0) {
                gsap.from(rows, {
                x: -200,          
                opacity: 0,       
                duration: 0.5,
                stagger: 0.3,      
                ease: "power2.out" 
                });
            }

            renderPagination(result.current_page, result.last_page);
            renderResultInfo(result);

        });
}

function renderPagination(current, last) {

    
    const container = document.getElementById("pagination");
    container.innerHTML = "";

    if (last <= 1) return;

    for (let i = 1; i <= last; i++) {

    const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = "btn btn-sm " + (i === current ? "btn-primary" : "btn-outline-primary");
        
        btn.addEventListener("click", function() {

            const status = document.getElementById("range").value;
            const limit = document.getElementById("limit").value;

            getData(status, limit, i);

        });

        container.appendChild(btn);
    }

    
}


function renderResultInfo(result) {

    const info = document.getElementById("pageInfo");

    if (result.total === 0) {
        info.textContent = "No results found";
        return;
    }

    const start = (result.current_page - 1) * result.data.length + 1;
    let end = result.current_page * result.data.length;

    if (end > result.total) {
        end = result.total;
    }

    info.textContent = `Showing ${start}–${end} of ${result.total} results`;
}







