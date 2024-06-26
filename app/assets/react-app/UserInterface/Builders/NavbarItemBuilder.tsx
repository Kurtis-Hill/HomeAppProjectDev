import * as React from 'react';
import { ListLinkItem } from '../Response/Navbar/NavBarResponseInterface';
import NavbarListItem from '../Components/NavbarListItem';


export function BuildNavbarItem(props: {
    heading: string,
    icon: string;
    listLinks: ListLinkItem[];
    createNewText: string;
    errors?: string[];
    flagAddNewModal?: (show: boolean) => void|null;
}) {
    return (
        <NavbarListItem
            header={props.heading}
            icon={props.icon}
            listLinks={props.listLinks}
            flagAddNewModal={props.flagAddNewModal}
            errors={props.errors}
            createNewText={props.createNewText}/>
    )
}
