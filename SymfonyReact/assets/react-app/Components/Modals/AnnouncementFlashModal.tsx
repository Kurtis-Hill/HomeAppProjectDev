import * as React from 'react';
import { useState } from 'react';

export function AnnouncementFlashModal(props) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  console.log('err', errors);
  let modalShow: boolean = props.modalShow;

  const [modalOpacity, setModalOpacity] = useState<number>(100);

    // if (modalShow === true) {
    //   const alterModalOpacity = setInterval(() => {
    //     setModalOpacity(modalOpacity - 1)
    //     console.log(modalOpacity);
    //     if (modalOpacity === 0) {
    //       clearInterval(alterModalOpacity);
    //     }
    //   }, 100)
      // style={modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}}
    }
    // announcement-fade
    return (
        <div style={modalShow !== false ? {paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%` } : {display: 'none'}} className="modal-show modal fade show show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div className="modal-dialog" role="document">
          <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title title">{title}</h5>
              </div>
              { 
                errors.length > 0 
                  ?
                    <div className="modal-body">
                      <React.Fragment>
                        {
                          errors.map((error, index) => (
                            <li key={index}>{error}</li>
                          ))
                        }
                      </React.Fragment>
                    </div>
                  : 
                    null
              }  
          </div>
        </div>
      </div>

    );
  }

  export function announcementTimeout(props) {
    const hi = this.modalShow;
  }