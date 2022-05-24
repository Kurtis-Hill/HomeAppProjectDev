<?php

namespace App\Common\API;

class APIErrorMessages
{
    public const MALFORMED_REQUEST_MISSING_DATA = 'Malformed request missing data';

    public const MALFORMED_REQUEST_MISSING_THIS_DATA = 'Malformed request missing %s data';

    public const OBJECT_NOT_FOUND = '%s not found';

    public const OBJECT_NOT_RECOGNISED = '%s not recognised';

    public const FAILED_TO_SAVE_DATA = 'Failed to save data';

    public const ACCESS_DENIED = 'You have been denied permission to perform this action';

    public const SHOULD_NOT_BE_BLANK = '%s name should not be blank';

    public const QUERY_FAILURE = '%s Query failure';

    public const FAILED_TO_PREPARE_DATA = 'Failed to prepare data';

    public const FAILED_TO_PREPARE_OBJECT_RESPONSE = 'Failed to prepare %s data';

    public const FORMAT_NOT_SUPPORTED = 'Format not supported';

    public const CONTACT_SYSTEM_ADMIN = '%s Contact your system admin';

    public const SERIALIZATION_FAILURE = '%s Serialization failure';

    public const VALIDATION_ERRORS = 'Validation errors occurred';

    public const COULD_NOT_PROCESS_ANY_CONTENT = 'None of the content could be processed';

    public const PART_OF_CONTENT_PROCESSED = 'Part of the content could not be processed';

    public const READING_TYPE_NOT_VALID_FOR_SENSOR = '%s reading type not valid for sensor: %s';

    public const FAILED_TO_NORMALIZE_RESPONSE = 'Failed to normalize response';

}
