import React from 'react';
export interface NavbarListInterface {
    heading: string;
    icon: string;
    listLinks: Array<NavbarListItemInterface>;
    createNewLink?: string|null;
    createNewText?: string|null;
    showAddNewElement?: React;
}

export interface NavbarListItemInterface {
    link: string;
    displayName: string;
}
