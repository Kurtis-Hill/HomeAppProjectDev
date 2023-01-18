export interface NavBarResponseInterface {
    title: string;
    payload: IndividualNavBarResponse[];
    errors?: Array<string>
}

export interface IndividualNavBarResponse {
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