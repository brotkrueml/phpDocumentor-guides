<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Settings;

class SettingsManager
{
    public function __construct(
        private ProjectSettings $projectSettings = new ProjectSettings(),
    ) {
    }

    public function getProjectSettings(): ProjectSettings
    {
        return $this->projectSettings;
    }

    public function setProjectSettings(ProjectSettings $projectSettings): void
    {
        $this->projectSettings = $projectSettings;
    }
}
