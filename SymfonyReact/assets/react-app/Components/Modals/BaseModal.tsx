import * as React from 'react';
import {useEffect, useState} from 'react';
import {Outlet} from "react-router-dom";

export default function BaseModal(props) {
    // const styles = props.styles ?? '';
    const keyValue = props.keyValue ?? '';
    const title = props.title ?? '';
    const content = props.content ?? '';
    const modalOpacity = props.modalOpactiy ?? '';

    const [styles, setStyles] = useState(props.styles ?? '');

    const [modalShow, setModalShow] = useState<boolean>(true);
    const toggleModalOff = (): void => {
        setModalShow(false);
    }

    // const [modalOpacity, setModalOpacity] = useState<number>(props.modalOpacity ?? 100);

    useEffect(() => {
        console.log('styles', styles)
        // setModalOpacity(props.modalOpacity ?? 0);
        // setStyles(props.styles ?? '');
    }, [modalOpacity]);


    if (modalShow === true) {
        return (
            <React.Fragment>
                <div key={keyValue} style={{paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%`}} className="modal-show modal"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title title">{title}</h5>
                                <button onClick={toggleModalOff} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            { content }
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
