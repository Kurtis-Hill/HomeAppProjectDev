import React from 'react';
import { JsxElement } from 'typescript';
export interface NavbarListInterface {
    heading: string;
    icon: string;
    listLinks: Array<string>;
    createNewLink?: string|null;
    createNewText?: string|null;
    flagAddNewModal?: (show: boolean) => void;
    errors?: Array<string>;
}

export interface NavbarListItemInterface {
    link: string;
    displayName: string;
}

// export interface ListItem
