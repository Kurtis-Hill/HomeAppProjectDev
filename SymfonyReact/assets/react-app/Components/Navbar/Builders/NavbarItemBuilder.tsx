import * as React from 'react';

import NavbarListItem from '../NavbarListItem'
import { NavbarListInterface } from '../Interfaces/NavbarItemInterfaces'

export function BuildNavbarItem(props: NavbarListInterface) {
    return (
        <NavbarListItem
            heading={props.heading}
            icon={props.icon}
            createNewLink={props.createNewLink}
            listLinks={props.listLinks}
            createNewText={props.createNewText}
        />
    )
}
