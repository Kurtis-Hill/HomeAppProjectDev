import * as React from 'react';


export interface NavbarList {
    heading: string;
    icon: string;
    listLinks: Array<NavbarListItem>;
    createNewLink: string|null;
    createNewText: string|null;
}

export interface NavbarListItem {
    link: string;
    displayName: string;
}
