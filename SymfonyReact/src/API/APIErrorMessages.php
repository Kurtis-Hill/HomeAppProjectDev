<?php

namespace App\API;

class APIErrorMessages
{
    public const MALFORMED_REQUEST_MISSING_DATA = 'Malformed request missing data';

    public const MALFORMED_REQUEST_MISSING_THIS_DATA = 'Malformed request missing %s data';

    public const OBJECT_NOT_FOUND = '%s not found';

    public const FAILED_TO_SAVE_DATA = 'Failed to save data';

    public const ACCESS_DENIED = 'You have been denied permission to perform this action';

    public const SHOULD_NOT_BE_BLANK = '%s name should not be blank';

    public const QUERY_FAILURE = '%s Query failure';

    public const FAILED_TO_PREPARE_DATA = 'Failed to prepare data';

    public const FORMAT_NOT_SUPPORTED = 'Format not supported';

    public const CONTACT_SYSTEM_ADMIN = '%s Contact your system admin';

    public const SERIALIZATION_FAILURE = '%s Serialization failure';
}
