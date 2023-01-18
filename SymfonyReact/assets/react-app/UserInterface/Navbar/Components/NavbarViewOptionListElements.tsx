import * as React from 'react';
import { useState, useEffect, useMemo } from 'react';


import { IndividualNavBarResponse, NavBarResponseInterface } from "../Response/NavBarResponseInterface";
import NavbarListItem from './NavbarListItem'
import { BuildNavbarItem } from '../Builders/NavbarItemBuilder';



export default function NavbarViewOptionListElements(props: {
    navbarResponseData: NavBarResponseInterface,
    showAddNewDeviceModalFlag: (show: boolean) => void,
}) {  
    const showAddNewDeviceModalFlag = props.showAddNewDeviceModalFlag;
    const navbarResponseData = props.navbarResponseData

    const navbarItems = useMemo(
        () => createNavListItems(props.navbarResponseData), 
        [navbarResponseData]
    );

    function createNavListItems(navbarResponseData: NavBarResponseInterface): React {
        const builtNavItems: Array<typeof NavbarListItem> = [];
        
        if (navbarResponseData.payload) {
            for (let i = 0; i < navbarResponseData.payload.length; i++) {
                const individualNavBarItem: IndividualNavBarResponse = navbarResponseData.payload[i];
                let showAddNewModalFlag: (show: boolean) => void|null = null;
                let addNewText: string = '+Add New';
                console.log('hi', individualNavBarItem);
                if (individualNavBarItem.itemName === 'devices') {
                    showAddNewModalFlag = showAddNewDeviceModalFlag;
                    addNewText = '+Add New Device';
                } 
                builtNavItems.push(
                    BuildNavbarItem({
                        heading: individualNavBarItem.header,
                        icon: individualNavBarItem.icon,
                        listLinks: individualNavBarItem.listItemLinks,
                        createNewText: addNewText,
                        errors: individualNavBarItem.errors,
                        flagAddNewModal: showAddNewModalFlag
                    })
                );
            }
        }
            
        return builtNavItems.map((item: typeof NavbarListItem, index: number) => {
            return (
                <React.Fragment key={index}>
                    {item}
                </React.Fragment>
            );
        });
    }
        
    return (
        <React.Fragment>
            { navbarItems }
        </React.Fragment>
    );
}
