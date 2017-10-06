# saiddit
Using Bootstrap, Jquery, JavaScript, PHP, CSS, HTML, MySQL
Note: You will have to download and install sweetalert for this to run properly.
### What has been done
1. UI layout using HTML, CSS, Jquery, and Bootstrap
2. Sign up capability
3. Log in capability
4. Basic error checking for sign up and log in forms. Check if all fields are filled out when logging in or signing up. Check if username is already taken when signing up.
5. Passwords are hashed before being entered into database using sha265
6. Users can add friends and remove friends. Basic error checking included such as checking for invalid user names and checking if user is actually in friends list when trying to remove. User can not add a friend that is already added in their friends list
7. Users can view friends they have added
8. When a user signs up, they are automatically subscribed to the default subsaiddits
9. Users can view a list of subsaiddits they are subscribed to
10. Basic design of subsaiddit pages. Subsaiddits still need to be filled. Should be done soon.
11. Users can create subsaiddits. Users automaticlly subscribe to subsaiddits they have created.
12. Users can subscribe to subsaiddits they choose
13. Users can create posts and delete a post. If logged in, home page shows posts only from subscribed subsaiddits. If not logged in, home page shows posts from all subsaiddits. 
14. users can upvote and down vote a post
15. post goes to a link of a webpage if there is one, when clicked


### What needs to be done
1. [Would be nice if fixed] Buttons need to be pressed twice for them to register. Sometimes page needs to be refreshed for   buttons to work.
2. Layout is buggy at some spots. You will notice this if you try to edit the style.css file.
3. Fix up how the code looks
4. Everything to do with post comments 
5. Making it so user can only upvote or downvote once, and never both at the same time. 
6. ive added screenshots of how i set up the databse tables in phpmyadmin sql. You will need to match these table names and their data field names for the project to run. Or you can edit the names inside the code to match your own table names and data names.  Note, for tables that have two foreign keys, you will have to make the column names indexes then go to relations view and add constraints to both of them. You might have to edit the port number in config.php based on how you set the project up. Can follow my config.php as a guide. For some reason, mine uses mysqli to connect. Yours may or may not be different.
 



