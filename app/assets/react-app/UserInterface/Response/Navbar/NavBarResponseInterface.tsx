export interface NavBarResponseInterface {
    title: string;
    payload: IndividualNavBarElement[];
    errors?: string[]
}

export interface IndividualNavBarElement {
    header: string;
    icon: string;
    itemName: string;
    listItemLinks: ListLinkItem[];
    errors?: string[];
}

export interface ListLinkItem {
    displayName: string;
    link: string;
}
