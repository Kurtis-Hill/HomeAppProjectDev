<?php


namespace App\API;


class HTTPStatusCodes
{
    public const HTTP_OK = 200;

    public const HTTP_CREATED = 201;

    public const HTTP_ACCEPTED = 202;

    public const HTTP_PARTIAL_CONTENT = 206;

    public const HTTP_BAD_REQUEST = 400;


    public const HTTP_UNAUTHORISED = 401;

    public const HTTP_FORBIDDEN = 403;

    public const HTTP_NOT_FOUND = 404;

    public const HTTP_NOT_ACCEPTABLE = 406;

    public const HTTP_REQUEST_TIMEOUT = 407;

    public const HTTP_INTERNAL_SERVER_ERROR = 500;

}

//    public const

//407 	Proxy Authentication Required 	[RFC7235, Section 3.2]
//408 	Request Timeout 	[RFC7231, Section 6.5.7]
//409 	Conflict 	[RFC7231, Section 6.5.8]
//410 	Gone 	[RFC7231, Section 6.5.9]
//411 	Length Required 	[RFC7231, Section 6.5.10]
//412 	Precondition Failed 	[RFC7232, Section 4.2][RFC8144, Section 3.2]
//413 	Payload Too Large 	[RFC7231, Section 6.5.11]
//414 	URI Too Long 	[RFC7231, Section 6.5.12]
//415 	Unsupported Media Type 	[RFC7231, Section 6.5.13][RFC7694, Section 3]
//416 	Range Not Satisfiable 	[RFC7233, Section 4.4]
//417 	Expectation Failed 	[RFC7231, Section 6.5.14]
//418-420 	Unassigned
//421 	Misdirected Request 	[RFC7540, Section 9.1.2]
//422 	Unprocessable Entity 	[RFC4918]
//423 	Locked 	[RFC4918]
//424 	Failed Dependency 	[RFC4918]
//425 	Too Early 	[RFC8470]
//426 	Upgrade Required 	[RFC7231, Section 6.5.15]
//427 	Unassigned
//428 	Precondition Required 	[RFC6585]
//429 	Too Many Requests 	[RFC6585]
//430 	Unassigned
//431 	Request Header Fields Too Large 	[RFC6585]
//432-450 	Unassigned
//451 	Unavailable For Legal Reasons 	[RFC7725]
//452-499 	Unassigned
//500 	Internal Server Error 	[RFC7231, Section 6.6.1]
//501 	Not Implemented 	[RFC7231, Section 6.6.2]
//502 	Bad Gateway 	[RFC7231, Section 6.6.3]
//503 	Service Unavailable 	[RFC7231, Section 6.6.4]
//504 	Gateway Timeout 	[RFC7231, Section 6.6.5]
//505 	HTTP Version Not Supported 	[RFC7231, Section 6.6.6]
//506 	Variant Also Negotiates 	[RFC2295]
//507 	Insufficient Storage 	[RFC4918]
//508 	Loop Detected 	[RFC5842]
//509 	Unassigned
//510 	Not Extended 	[RFC2774]
//511 	Network Authentication Required 	[RFC6585]