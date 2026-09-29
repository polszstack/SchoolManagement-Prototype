<?php
/**
 * SettingsApiController
 * Handles settings-related API actions:
 *   get_settings | save_settings
 */
class SettingsApiController
{
    private Settings    $settings;
    private ActivityLog $log;

    public function __construct()
    {
        $this->settings = new Settings();
        $this->log      = new ActivityLog();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(string $action, array $input): never
    {
        match ($action) {
            'get_settings'  => $this->getSettings(),
            'save_settings' => $this->saveSettings($input),
            default         => Response::error("Unknown settings action: {$action}"),
        };
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private function getSettings(): never
    {
        Response::json($this->settings->all());
    }

    private function saveSettings(array $input): never
    {
        $this->settings->save($input);
        $this->log->log('Settings Updated', 'Library settings updated');
        Response::success('Settings saved!');
    }
}
