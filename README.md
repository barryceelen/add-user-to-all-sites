# Add User to All Sites WordPress CLI Command

Registers a CLI command that allows you to add a user to all sites in a multisite network.

## Use

`wp add_user_to_all_sites --email=test@example.com --role=administrator`

The command accepts two options:

**email**

The email address of the user.

**role**

The role that should be assigned to the user on each site. Defaults to `subscriber` if not set. If the user already exists on a site its role will be updated.
