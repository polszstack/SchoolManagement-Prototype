 document.addEventListener('DOMContentLoaded', function () {

        setTimeout(() => {

            if(!localStorage.getItem('isDoneTutorial'))
            {
                introDriver();
            }

        }, 5000);



 })
 

 
 
 
 function introDriver()
    {

        const driver = window.driver.js.driver;

        const driverObj = driver({
        showProgress: true,
        allowClose: false,

        onDestroyed: () => {
            localStorage.setItem('isDoneTutorial', 'true');
        },


        steps: [
            { element: '#dashboard', popover: {description: '<h5>Dashboard</h5><br>This is where you’ll find a summary of all the key information at a glance. Check student counts, recent activity, and upcoming events.'} },
            { element: '#StudentDriver', popover: {description: '<h5>Student Management</h5><br>This is where you’ll find a student data and informations.'} },
            { element: '#AcademicDriver', popover: { description: '<h5>Academic Managment</h5><br>This is where you find the academic data setups and managements .' } },
             { element: '#ReportDriver', popover: { description: '<h5>Report and Requests</h5><br>Sections for reports and other Submissions' } },
            { element: '#ToolDriver', popover: { description: '<h5>Tools</h5><br>Tools that may help the user to make job more efficient. ' } },
             { element: '#SettingDriver', popover: { description: '<h5>Settings</h5><br>Section for other data such as privacy information' } },
            { popover: { description: '<h5>Welcome Aboard!</h5><br>You may proceed. Please go ahead and try using the system.' } }
        ]
        });

        driverObj.drive();

    }