# Bank App
## Distinctiveness and Complexity

### Distinctiveness
Previous projects was a Social Network, Email application, E-Commerce website and Wiki application. This final project is a Bank application which allows users to Send and Receive transactions from and to other users.

### Complexity
In this project I use two applications diferents from the main project. One application is used as front-end. The second one is used as a REST API back-end. Communication is perform in JSON format. Security validations have been thought of in the design phase:

* The application is protected agains Cross-site Request Forgery.
* The application is protected agains Insecure Direct Object References. Logged user can access only to product information that belongs to him. This include accounts balance, beneficiary booklist, transaction historical data, etc.
* Protection agains Business logic abuses. Validating that origin account have funds before making a transfer, reject transactions with negative or zero amounts, etc. 
  
Others
* Communication architecture of the application is client - server.
* Dependencies are handled with Poetry. 
* Correct use of Decimal format for the amount in transfers.
* Use of custom messages for each action performed by the user.

## Getting Started

1. Download the distribution code from https://github.com/me50/itsecurityco/archive/refs/heads/web50/projects/2020/x/capstone.zip and unzip it.