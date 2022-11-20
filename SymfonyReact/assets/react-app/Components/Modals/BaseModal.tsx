import * as React from 'react';
import { useState } from 'react';
import {Outlet} from "react-router-dom";

export default function BaseModal(props) {
    const styles = props.styles ?? '';
    const keyValue = props.keyValue ?? '';
    const title = props.title ?? '';

    const [modalShow, setModalShow] = useState<boolean>(true);
    const toggleModalOff = (): void => {
        setModalShow(false);
    }

    if (modalShow === true) {
        return (
            <React.Fragment>
                <div key={keyValue} style={{ styles }} className="modal-show modal"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title title">{title}</h5>
                                <button onClick={toggleModalOff} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                             <Outlet context={toggleModalOff} />
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
