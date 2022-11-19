export interface NavbarListInterface {
    heading: string;
    icon: string;
    listLinks: Array<NavbarListItemInterface>;
    createNewLink: string|null;
    createNewText: string|null;
}

export interface NavbarListItemInterface {
    link: string;
    displayName: string;
}
