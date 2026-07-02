<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\ClearsContactMetricsCache;
use App\Models\Contact;

final class ContactObserver
{
    use ClearsContactMetricsCache;

    public function created(Contact $contact): void
    {
        $this->clearContactMetricsCache($contact->user_id);
    }

    public function updated(Contact $contact): void
    {
        $this->clearContactMetricsCache($contact->user_id);
    }

    public function deleted(Contact $contact): void
    {
        $this->clearContactMetricsCache($contact->user_id);
    }

    public function restored(Contact $contact): void
    {
        $this->clearContactMetricsCache($contact->user_id);
    }

    public function forceDeleted(Contact $contact): void
    {
        $this->clearContactMetricsCache($contact->user_id);
    }
}
