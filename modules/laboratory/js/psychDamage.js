 document.getElementById("psychoAddDamageBtn").addEventListener('click',function(){

    let psychoAddDamageModal = new bootstrap.Modal(document.getElementById('psychoAddDamageModal'))
    psychoAddDamageModal.show();

 });

   document.querySelectorAll('.viewPsychoDamageBtn').forEach(button => {
        button.addEventListener("click", function () {

            let id = this.getAttribute("data-id");

            let modal = new bootstrap.Modal(document.getElementById('psychoViewDamageModal'));
            modal.show();

            document.getElementById("damage_id").value = id;
      
            fetch(`${BASE_URL}/psycho-damage/view/${id}`)
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


    
      document.querySelectorAll('.editPsychoDamageBtn').forEach(button =>{

        button.addEventListener('click', function(){

            let psychoEditDamageModal = new bootstrap.Modal(document.getElementById('psychoEditDamageModal'));
             psychoEditDamageModal.show();

            let id = this.getAttribute("data-id");

            fetch(`${BASE_URL}/psycho-damage/view/${id}`)
                .then(response => response.json())
                .then(data => {

                    document.getElementById("damage_id").value = data.id;
                    document.getElementById("item").value = data.category;
                    document.getElementById("description").value = data.description;
                    document.getElementById("code").value = data.code;
                    document.getElementById("status").value = data.status;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });


        });

    })


    document.getElementById('editPsychoDamageForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/psycho-damage/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            let psychoEditDamageModal = new bootstrap.Modal(document.getElementById('psychoEditDamageModal'));
             psychoEditDamageModal.show();
            location.reload();

        } else {
            alert('Error: ' + data.error);
        }


    })
    .catch(
        
        err => console.error('Error updating damage:', err)
    );
});


    document.querySelectorAll('.deleteBtn').forEach( button => {

        button.addEventListener('click', function() {

            let id = this.getAttribute("data-id");

        if(confirm("Are you sure you want to delete this damage?")) {
            fetch(`${BASE_URL}/psycho-damage/delete/${id}`, {
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

