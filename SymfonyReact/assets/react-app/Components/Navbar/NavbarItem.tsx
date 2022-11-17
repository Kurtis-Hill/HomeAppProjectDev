import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link, useNavigate } from "react-router-dom";

import { webappURL } from "../../Common/CommonURLs";

export default function NavbarItem(props) {
    const heading: string = props.heading;
    const objectsToList: any = props.dropDownItems;

    console.log('lol', objectsToList)
    const [dropdownItems, setDropdownItems] = useState<Array<any>>([]);
    
    const [navbarItemToggleDropdown, setNavbarItemToggleDropdown] = useState<boolean>(false)
    
    const toggleNavTabElement = (): void => {            
        setNavbarItemToggleDropdown(!navbarItemToggleDropdown);
    }
    
    const navItemDropdownToggleClass: string = navbarItemToggleDropdown === true ? 'show' : '';
    
    // useEffect(() => {
    //     console.log(props.dropDownItems, ',e', heading, objectsToList);
    //     setDropdownItems(props.dropDownItems);
    //   }, [props.dropDownItems]);

    return (
        <li className="nav-item" onClick={() => {toggleNavTabElement()}}>
            <div className="nav-link collapsed hover" data-toggle="collapse" aria-expanded="true" aria-controls="collapseUtilities">
                <i className="fas fa-fw fa-person-booth"/>
                <span>{ heading }</span>
            </div>
            <div className={`collapse ${navItemDropdownToggleClass}`} aria-labelledby="headingTwo">
                <div className="bg-white py-2 collapse-inner rounded">
                    <h6 className="collapse-header">View Room:</h6>
                    {
                        Array.isArray(objectsToList) && objectsToList.length > 0
                            ? objectsToList.map((navRoom: { groupNameID: number; roomID: number; roomName: string }, index: number) => (
                            <Link to={`${webappURL}room?room-id=${navRoom.roomID}`} key={index} className="collapse-item">{navRoom.roomName}</Link>
                        ))
                            : null
                    }
                    <Link to={`${webappURL}add-room`} className="collapse-item">+Add New Room</Link>
                </div>
            </div>
        </li>
    );
}