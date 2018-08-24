# Password Policy

[![codecov](https://codecov.io/gh/owncloud/password_policy/branch/master/graph/badge.svg?token=JoJt5NmSSC)](https://codecov.io/gh/owncloud/password_policy)

The Password Policy extension enables ownCloud administrators to define password requirements like minimum characters, numbers, capital letters and more for all kinds of password endpoints like user account and public link sharing passwords. To add another layer of security, the administrator can enforce expiration dates for public link shares depending on whether a password has been set or not. As a further measure the extension saves a history of hashed user passwords to prevent users from choosing a former password again, enforcing password security even more. Users can also be required to change their password upon first login. To impose regular password changes administrators can set up password expiration policies. For this users can be notified via email, web interface and the ownCloud Clients when their password is about to expire and when it has expired.

The definition of certain password rules support administrators in the task of ensuring a minimum level of password security throughout the enterprise. It minimizes the risk of weak user passwords and therefore adds an additional security aspect to ownCloud. The expiration date policies for public link shares allow users to depart from general public link expiration policies. Users can, for instance, be allowed to create longer-lasting public link shares when they choose to set a password. This way IT can provide more flexibility in external sharing while staying in full control.
Password history and expiration policies are supplements that allow IT to establish a level of password security that can comply with corporate guidelines of all sorts. The provided tools enable administrators to granularly choose their desired security level. At this point it is important to keep in mind that high levels of security might sacrifice usability and come at the expense of user experience. For this reason it is highly recommended to check [best practices](https://pages.nist.gov/800-63-3/sp800-63b.html) and decide carefully on the hurdles that are put upon users in order to maintain and optimize user adoption and satisfaction.

Administrators find the configuration options in the 'Security' section of the ownCloud administration settings panel. The respective policies are designed for local user accounts created by administrators or via the [Guests](https://marketplace.owncloud.com/apps/guests) extension, not for user accounts imported from LDAP or other user backends as these provide their own mechanisms. For more information and recommendations when deploying policies in an existing ownCloud, please consult the [ownCloud Documentation](https://doc.owncloud.com/server/latest/admin_manual/configuration/server/security/password_policy.html).
