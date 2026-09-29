let CreateDamageBtn = document.getElementById("CreateDamageBtn");
let viewButtons = document.querySelectorAll(".viewDamageBtn");
let editButton = document.querySelectorAll(".editDamageBtn");
let deleteBtn = document.querySelectorAll(".deleteBtn");


CreateDamageBtn.addEventListener("click", function() {
    let addDamageModal = new bootstrap.Modal(document.getElementById("addDamageModal"));
    addDamageModal.show();
});


    viewButtons.forEach(button => {
        button.addEventListener("click", function () {

            let id = this.getAttribute("data-id");

            let modal = new bootstrap.Modal(document.getElementById('viewDamageModal'));
            modal.show();

            document.getElementById("damage_id").value = id;
      
            fetch(`${BASE_URL}/damages/view/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("damage_id").textContent = data.id;
                    document.getElementById("damage_code").textContent = data.code;
                    document.getElementById("damage_category").textContent = data.category;
                    document.getElementById("damage_description").textContent = data.description;
                    document.getElementById("damage_status").textContent = data.status;
                    
                    let statusEl = document.getElementById("damage_status")

                    if (data.status === "Fixed") {
                        statusEl.classList.remove("bg-danger");
                        statusEl.classList.add("bg-success");
                    } else {
                        statusEl.classList.remove("bg-success");
                        statusEl.classList.add("bg-danger");
                    }

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });
               

        });
    });

    // edit modal 


    editButton.forEach( button => {

            button.addEventListener('click',function(){

             let id = this.getAttribute("data-id");

             let modal = new bootstrap.Modal(document.getElementById('editDamageModal'));
             modal.show();


              fetch(`${BASE_URL}/damages/edit/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('damage_id').value = data.id
                    document.getElementById('item').value = data.category;
                    document.getElementById("description").value = data.description;
                    document.getElementById('status').value = data.status;
                      document.getElementById('code').value = data.code;
                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });
            
            });



           
    });


document.getElementById('editDamageForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/damages/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const modalEl = document.getElementById('editDamageModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            location.reload();

        } else {
            alert('Error: ' + data.error);
        }


    })
    .catch(
        
        err => console.error('Error updating damage:', err)
    );
});

    // deletion 

    deleteBtn.forEach( button => {

        button.addEventListener('click', function() {

            let id = this.getAttribute("data-id");

        if(confirm("Are you sure you want to delete this damage?")) {
            fetch(`${BASE_URL}/damages/delete/${id}`, {
                method: "POST"
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                   location.reload()
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => console.error('Error deleting damage:', err));
        }
    });


    })





  


   
