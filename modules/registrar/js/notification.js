  document.addEventListener('DOMContentLoaded', function() {

    let notifBtn = document.getElementById('notif-btn');

    const notifModal = new bootstrap.Modal(document.getElementById('notif-modal'));


    notifBtn.addEventListener('click',function(){

        notifModal.show();

    });
    




  });