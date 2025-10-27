# Migrating from Combodo's Mail to Ticket Automation

1. Disable the cron job (task) for Combodo's Mail to Ticket Automation. Ensure no job is running anymore.
2. Remove any configured mail boxes using the iTop console (web interface) first.
3. Remove the folder containing Combodo's Mail to Ticket Automation.
4. Move the emails which were already processed into another folder or delete them.
5. Install this fork of Combodo's Mail to Ticket Automation instead, just like you would install any extension.
6. Run the iTop setup wizard.
7. Verify in iTop's about/support menu (top right power button -> support) that only this version of Mail to Ticket Automation has been installed.
8. Settings are **NOT** automatically copied. So take care of the settings and configuration, similar to Combodo's Mail to Ticket Automation. Verify connection.
9. Don't forget to re-enable the cron job (task).

