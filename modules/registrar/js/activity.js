  
  document.addEventListener('DOMContentLoaded', function() {
    
    const selectAll = document.getElementById('select-all');
   const tbody = document.querySelector('tbody');
   const deleteBtn = document.getElementById('delete-btn');

    let currentRange = 'today';
    let currentLimit = 10;
    let currentPage = 1;


    getData(currentRange, currentLimit, currentPage);


    document.getElementById("range").addEventListener('change', (e) => {

        currentRange = e.target.value;
        currentPage = 1;
        getData(currentRange, currentLimit,currentPage);

    });


    
    document.getElementById("limit").addEventListener('change', (e) => {


        currentLimit = e.target.value;
        currentPage = 1; 

        getData(currentRange, currentLimit, currentPage);

    });


    document.getElementById("search").addEventListener("input", function(e) {

    const status = document.getElementById("range").value;
    const limit = document.getElementById("limit").value;
    
    getData(status, limit, 1); 

    });



    document.getElementById('pdf').addEventListener('click',()=>{

        const range = document.getElementById("range").value;
         getPdf(range);

    });

    document.getElementById('excel').addEventListener('click',()=>{

       const range = document.getElementById("range").value;
       getExcel(range);

    });

    document.getElementById('csv').addEventListener('click',()=>{

       const range = document.getElementById("range").value;
       getCsv(range);

    });



    // buttons  


    function updateDeleteButton() {
        const checkedCount = tbody.querySelectorAll('.activity-checkbox:checked').length;
        deleteBtn.classList.toggle('d-none', checkedCount === 0);
    }

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

            fetch(`${BASE_URL}/activity/delete`, {
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
                    getData(currentRange, currentLimit, currentPage);
                } else {
                    alert('Delete failed!');
                }
            });
            
        }
        });

    

    });




 
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));


    document.getElementById("studentsTableBody").addEventListener("click", function(e) {

    if (e.target.classList.contains("view-btn")) {

        const studentId = e.target.dataset.id;

        fetch(`${BASE_URL}/activity/${studentId}`)
        .then(response => response.json())
         .then(result => {
          
            document.getElementById('modalTitle').textContent = result.action;
            document.getElementById('desc').textContent = result.description;
            document.getElementById('time').value = new Date(result.created_at).toLocaleString('en-PH', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
            document.getElementById('user').value = result.user_id ?? 'null';
            document.getElementById('ip_address').value = result.ip_address;


            viewModal.show();


         });

        
  
    }
    });
    
    });





    /*  ========================================================================================= 
       |                                                                                         |
       |     End of Dom Content                                                                  |
       |                                                                                         |
       ========================================================================================= 
    */



    // pdf data 

    function getPdf(range)
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
        `${BASE_URL}/activity/pdf?range=${range}&search=${encodeURIComponent(search)}`,
        "_blank"
        );
    }


    function getExcel(range)
    {

     
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/activity/excel?range=${range}&search=${encodeURIComponent(search)}`;

    }


    function getCsv(range)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/activity/csv?range=${range}&search=${encodeURIComponent(search)}`;
    }




    // get data from students 

    function getData(range, limit, page = 1){

    const search = document.getElementById("search").value;
    const tbody = document.getElementById("studentsTableBody");

    fetch(`${BASE_URL}/activity/all?range=${range}&limit=${limit}&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(result => {

            tbody.innerHTML = "";

            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">No result found</td></tr>`;
                return;
            }

            result.data.forEach(activity => {
                tbody.innerHTML += `
                    <tr class="activity-row">

                     <td><input type="checkbox" class="activity-checkbox" value="${activity.id}"></td>
                        <td>${activity.user_id}</td>
                        <td>${activity.action} </td>
                        <td>${activity.ip_address}</td>
                        <td> ${new Date(activity.created_at).toLocaleString('en-PH', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        })}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary view-btn" data-id="${activity.id}">
                                View
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

    const ul = document.createElement("ul");
    ul.className = "pagination";

    let start = Math.max(1, current - 2);
    let end = Math.min(last, current + 2);



    if (current > 1) {
        const li = document.createElement("li");
        li.className = "page-item";

        const link = document.createElement("a");
        link.className = "page-link";
        link.href = "#";
        link.innerHTML = "&laquo;";

        link.addEventListener("click", function(e){
            e.preventDefault();

            const status = document.getElementById("range").value;
            const limit = document.getElementById("limit").value;
            getData(status, limit, current - 1);
        });

        li.appendChild(link);
        ul.appendChild(li);
    }



    for (let i = start; i <= end; i++) {

        const li = document.createElement("li");
        li.className = "page-item " + (i === current ? "active" : "");

        const link = document.createElement("a");
        link.className = "page-link";
        link.href = "#";
        link.textContent = i;

        link.addEventListener("click", function(e) {
            e.preventDefault();

            const status = document.getElementById("range").value;
            const limit = document.getElementById("limit").value;

            getData(status, limit, i);
        });

        li.appendChild(link);
        ul.appendChild(li);
    }

    if (current < last) {
        const li = document.createElement("li");
        li.className = "page-item";

        const link = document.createElement("a");
        link.className = "page-link";
        link.href = "#";
        link.innerHTML = "&raquo;";

        link.addEventListener("click", function(e){
            e.preventDefault();

            const status = document.getElementById("range").value;
            const limit = document.getElementById("limit").value;
            getData(status, limit, current + 1);
        });

        li.appendChild(link);
        ul.appendChild(li);
    }


    container.appendChild(ul);


    
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







