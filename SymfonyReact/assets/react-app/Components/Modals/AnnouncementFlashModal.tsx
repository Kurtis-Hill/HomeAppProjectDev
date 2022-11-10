import * as React from 'react';
import { useState, useEffect } from 'react';

export function AnnouncementFlashModal(props: { title: string; errors: string[]; }) {
  const title: string = props.title
  const errors: Array<string> = props.errors

  const [modalOpacity, setModalOpacity] = useState<number>(100);
  const [modalShow, setModalShow] = useState<boolean>(true);

  useEffect(() => {
    console.log('use effect is running')
    const interval = setInterval(() => {
      console.log('modal op', modalOpacity);
      if (modalOpacity !== 0) {
        setModalOpacity(modalOpacity - 1);
        console.log('happened');
      } else {
        setModalShow(false);
        clearInterval(interval)
      }
    }, 80);
    return () => clearInterval(interval);
  }, [modalOpacity]);


  if (modalShow === true) {
    return (
      <div style={{ paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%` }} className="modal-show modal fade show show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
}