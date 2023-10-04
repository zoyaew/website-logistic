
# Sales Administration Portal

Sales Administration Portal is a website developed using HTML and PHP. It is hosted by XAMPP web server using Apache and MySQL.

------------

#### Currently, there are 5 main pages on the website:

 - [index.php](https://github.com/zoyaew/website-logistic/blob/main/code/index.php)
 
 A simple user login portal with two fields: username and password. User can choose whether to log-in using their username or email.
 
 All fields are sanitized and compared to the existing users in the database.
 After verified, user may log in. Session will start and a randomly generated session token with expiration time will be stored in the database.
 
- [home.php](https://github.com/zoyaew/website-logistic/blob/main/code/home.php)

 Home page shows sales transaction history of the current user (we assume the user is a salesman).
 
 There are several filters available:
 - Column customer name (radio button): Show or Hide
 - Column product name (radio button): Show or Hide
 - Column order description (radio button): Show or Hide
 - Column payment deadline: From Date, To Date
 - Column shipment deadline: From Date, To Date
 - Column order status (checkbox): Awaiting Payment, Awaiting Shipment, Completed, Cancelled
 - Pagination size: 20 - 100 per page
 - Column customer ID: a search bar
 - Column product ID: a search bar
 
 Once the filter is activated, a table will display the table data. The number of records matching the filters will be shown at the bottom of the page, as well as two buttons: "<", ">" which corresponds to showing the previous and the next page of the table data respectively.

- [update_order.php](https://github.com/zoyaew/website-logistic/blob/main/code/update_order.php)

 On this page, user can update the order status of a transaction record.
 
 Firstly, user will type in a receipt number to check if it exists. Once confirmed, there will be a drop down list containing the permitted order status change, i.e.:
 - Awaiting Payment -> Awaiting Shipment, Completed, Cancelled
 - Awaiting Shipment -> Completed, Cancelled
 - Completed, Cancelled: Status change is not permitted
 
After submitting the status change, user may refer to the home page to see the updated status.

- [new_order.php](https://github.com/zoyaew/website-logistic/blob/main/code/new_order.php)
 
 Everything related to making new records will be on this page.
 
 There are two buttons: &quot;add new customer&quot; or &quot;add new transaction&quot;
 
 - Add New Customer
 
   A new customer ID may be created using this function by entering their name and address. After submission, the particular newly created customer ID will be shown.
   
 - Add New Transaction
 
   To create a new order, one is required to put down the customer ID, product ID, quantity, and downpayment option. Other non-required fields include: order description, payment deadline, and shipping deadline. If not otherwise specified, payment deadline and shipping deadline will be defaulted to 2 weeks and 4 weeks date from ordering date automatically.
   
   Once all the fields are inputted, the fields will be shown once more alongside the calculated total price to be confirmed. Two buttons will appear: &quot;cancel new transaction&quot; and &quot;submit new transaction&quot;; to which, user can choose whether to cancel or submit the new order.
 
- [profile.php](https://github.com/zoyaew/website-logistic/blob/main/code/profile.php)

  This page shows the personal information of the current user by utilising a session to save the employee ID.
  Through the ID, it then queried the employee table database to show the first name, last name, username, email, branch, position, and manager name of the user.
  
  There is a button to change user&apos;s password. Once clicked, two fields will appear: &quot;New Password&quot; and &quot;Confirm Password&quot;. If the same inputs to both fields are submitted, the password will be changed.
  
  User can try to log-out and re-log-in to check the updated password. Once logged out, the session and the saved session token in the database will be destroyed.
  
  

------------

#### Extras:

- Check out the database scheme [here](https://github.com/zoyaew/website-logistic/blob/main/database_structure/database_scheme.txt)
- Check out the showcase video on youtube [here](https://youtu.be/Laq7NwCrB84)

------------
