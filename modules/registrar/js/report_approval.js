  
  document.addEventListener('DOMContentLoaded', function() {
    
   const selectAll = document.getElementById('select-all');
   const tbody = document.querySelector('tbody');
   const deleteBtn = document.getElementById('delete-btn');
   const addReportApproval = document.getElementById('addReportApproval');




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

                fetch(`${BASE_URL}/reports-approval/delete`, {
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

    addReportApproval.addEventListener('click',function(){

       let addApprovalModal = new bootstrap.Modal(document.getElementById('addApprovalModal'));
        addApprovalModal.show();
    });


    // submit button connect to form

    document.getElementById('addReportApprovalSubmit').addEventListener('click', function() {

    document.getElementById('addReportApprovalForm').requestSubmit();
       
    });


    // close button with reset

    document.getElementById('closeBtn').addEventListener('click',function(){

    resetForm('addReportApprovalForm');

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

   document.getElementById('addReportApprovalForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const formData = new FormData(this);

    fetch(this.action, { 
        method: 'POST',    
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        // Clear previous errors
        document.querySelectorAll('.error').forEach(el => el.innerText = '');
        document.querySelectorAll('.form-control').forEach(input => input.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

        if (data.status === 'error') {
            for (let field in data.errors) {
                const input = document.getElementById(field);
                const feedback = document.getElementById('error-' + field);
                
                if (input) input.classList.add('is-invalid');        
                if (feedback) feedback.innerText = data.errors[field]; 
            }
        } else if (data.status === 'success') {
            this.reset();

            document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

            getData(currentOrder, currentLimit, currentPage);

            const addReportApprovalModal = bootstrap.Modal.getInstance(document.getElementById('addApprovalModal'));
            addReportApprovalModal.hide();

            Swal.fire({
                title: "Success!",
                text: data.message,
                icon: "success"
            });
        }
    })
    .catch(err => console.log(err));
});

    // updating the toggles in status 

        tbody.addEventListener('change', function(e) {
        if(e.target.classList.contains('status-toggle')) {

            
            const allSameToggles = tbody.querySelectorAll('.status-toggle');

            if(e.target.checked){

            const checkedToggleId = e.target.dataset.id;


            
          Swal.fire({
            title: "Are you sure?",
            text: "This will permanently modify important data. Do you wish to proceed?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: `Yes, I understand`,
            }).then((result) => {
            if (result.isConfirmed) {
            
              fetch(`${BASE_URL}/school-year/${checkedToggleId}/update`, 
                    {
                        method: 'POST',
                        headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({ checkedToggleId })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(result => {
                         allSameToggles.forEach(checkbox => {
                            if(checkbox != e.target){
                            checkbox.checked = false; 
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    
                }else{
                     e.target.checked = false;
                }
             });

            }
        }
    });

   document.getElementById("studentsTableBody").addEventListener("click", function(e) {

    if (e.target.classList.contains("approved")) {

        const reportId = e.target.dataset.id;

        fetch(`${BASE_URL}/reports-approval/${reportId}/approved`, {
            method: "POST"
        })
        .then(response => response.json())
        .then(result => {

               let currentOrder = 'DESC';
                let currentLimit = 10;
                let currentPage = 1;
                getData(currentOrder, currentLimit, currentPage);


        });

    }

        if (e.target.classList.contains("reject")) {

            const reportId = e.target.dataset.id;

            fetch(`${BASE_URL}/reports-approval/${reportId}/reject`, {
            method: "POST"
        })
        .then(response => response.json())
        .then(result => {

               let currentOrder = 'DESC';
                let currentLimit = 10;
                let currentPage = 1;
                getData(currentOrder, currentLimit, currentPage);


        });

            
        }

    });
 

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
        `${BASE_URL}/school-year/pdf?order=${order}&search=${encodeURIComponent(search)}`,
        "_blank"
        );
    }


    function getExcel(order)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/school-year/excel?range=${order}&search=${encodeURIComponent(search)}`;
    }


    function getCsv(order)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/school-year/csv?order=${order}&search=${encodeURIComponent(search)}`;
    }




    // get data from students 

    function getData(order, limit, page = 1){

    const search = document.getElementById("search").value;
    const tbody = document.getElementById("studentsTableBody");

    fetch(`${BASE_URL}/reports-approval/all?order=${order}&limit=${limit}&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(result => {

            tbody.innerHTML = "";

            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">No approvals found.</td></tr>`;
                return;
            }

            result.data.forEach((report)=> {
                tbody.innerHTML += `
                    <tr class="activity-row">

                     <td><input type="checkbox" class="activity-checkbox" value="${report.approval_id}"></td>
                        <td>${report.approval_id}</td>
                        <td>${report.title}</td>
                        <td>${report.submitter}</td>
                        <td>${report.department}</td>
                        <td>${report.approver}</td>
                        <td>${report.decision}</td>
                        <td>
                        ${report.file_path 
                                ? `<a style="color:var(--color2); text-decoration:none;" href="${report.file_path}" target="_blank">View</a>` 
                                : `<span class="text-muted">No file</span>`
                            }
                        </td>

                       <td>
                        <button class="btn btn-primary btn-sm approved" data-id="${report.approval_id}">
                            Approve
                        </button>
                        <button class="btn btn-danger btn-sm reject" data-id="${report.approval_id}">
                            Reject
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

            const status = document.getElementById("order").value;
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







