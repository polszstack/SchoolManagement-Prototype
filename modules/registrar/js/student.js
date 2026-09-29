  
  document.addEventListener('DOMContentLoaded', function() {
    
    let currentStatus = 'all';
    let currentLimit = 10;
    let currentPage = 1;


    getData(currentStatus, currentLimit, currentPage);

    document.getElementById("status").addEventListener('change', (e) => {

        currentStatus = e.target.value;
        currentPage = 1;
        getData(currentStatus, currentLimit,currentPage);

    });


    
    document.getElementById("limit").addEventListener('change', (e) => {


        currentLimit = e.target.value;
        currentPage = 1; 

        getData(currentStatus, currentLimit, currentPage);

    });


    document.getElementById("search").addEventListener("input", function(e) {

    const status = document.getElementById("status").value;
    const limit = document.getElementById("limit").value;
    

    getData(status, limit, 1); 

    });



    document.getElementById('pdf').addEventListener('click',()=>{

        const status = document.getElementById("status").value;
       getPdf(status);

    });

    document.getElementById('excel').addEventListener('click',()=>{

       const status = document.getElementById("status").value;
       getExcel(status);

    });

    document.getElementById('csv').addEventListener('click',()=>{

       const status = document.getElementById("status").value;
       getCsv(status);

    });



    // buttons  

    document.getElementById("studentsTableBody").addEventListener("click", function(e) {

    if (e.target.classList.contains("view-btn")) {

        const studentId = e.target.dataset.id;

        alert(studentId);
  
    }
    });

    });

     /*  ========================================================================================= 
       |                                                                                         |
       |      End of Dom Content                                                                 |
       |                                                                                         |
       ========================================================================================= 
    */


    // pdf data 

    function getPdf(status)
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
        `${BASE_URL}/students/pdf?status=${status}&search=${encodeURIComponent(search)}`,
        "_blank"
        );
    }


    function getExcel(status)
    {

     
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/students/excel?status=${status}&search=${encodeURIComponent(search)}`;

    }


    function getCsv(status)
    {
        const search = document.getElementById("search").value;
        window.location.href = `${BASE_URL}/students/csv?status=${status}&search=${encodeURIComponent(search)}`;
    }




    // get data from students 

    function getData(status, limit, page = 1){

    const search = document.getElementById("search").value;
    const tbody = document.getElementById("studentsTableBody");

    fetch(`${BASE_URL}/students/all?status=${status}&limit=${limit}&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(result => {

            tbody.innerHTML = "";

            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">No students found</td></tr>`;
                return;
            }

            result.data.forEach(student => {
                tbody.innerHTML += `
                    <tr class="student-row">
                        <td>${student.student_number}</td>
                        <td>${student.first_name} ${student.last_name}</td>
                        <td>${student.course}</td>
                        <td>${student.academic_status}</td>
                        <td>
                            <button class="btn btn-sm btn-primary view-btn" data-id="${student.student_number}">
                                View
                            </button>
                        </td>
                    </tr>
                `;
            });

            const rows = tbody.querySelectorAll(".student-row");

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

            const status = document.getElementById("status").value;
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

            const status = document.getElementById("status").value;
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

            const status = document.getElementById("status").value;
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







