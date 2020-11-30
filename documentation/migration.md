# Migrating from Combodo's Mail to Ticket Automation

1. Disable the cron job (task) for Combodo's Mail to Ticket Automation. Ensure no job is running anymore.
2. Remove the folder containing Combodo's Mail to Ticket Automation
3. Move the emails which were already processed into another folder or delete them.
4. Install this fork of Combodo's Mail to Ticket Automation instead, just like you would install any extension.
5. Re-run the setup.
6. Verify in iTop's about/support menu (top right power button -> support) that only this version of Mail to Ticket Automation has been installed.
6. Settings are **NOT** automatically copied. So take care of the settings and configuration, similar to Combodo's Mail to Ticket Automation. Verify connection.
7. Don't forget to re-enable the cron job (task).

