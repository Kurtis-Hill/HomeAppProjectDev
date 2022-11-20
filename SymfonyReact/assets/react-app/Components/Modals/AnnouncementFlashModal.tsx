import * as React from 'react';
import {useState, useEffect, Context} from 'react';
import {useOutletContext} from "react-router-dom";
import BaseModal from "./BaseModal";

export function AnnouncementFlashModal(props: { title: string; errors: string[]; errorNumber: number; timer: number; }) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  const errorNumber: number = props.errorNumber
  const timer: number = props.timer;

  const [modalOpacity, setModalOpacity] = useState<number>(100);
  const [modalShow, setModalShow] = useState<boolean>(true);

  // const [toggleModalOff]: Context<Array<() => void>> = useOutletContext();

  useEffect(() => {
    const interval = setInterval(() => {
      console.log('modalOpacity', modalOpacity, modalShow)
      if (modalOpacity !== 0) {
        setModalOpacity(modalOpacity - 1);
      } else {
        setModalOpacity(100);
        // setModalShow(false);
        // clearInterval(interval)
      }
    }, timer);

    return () => clearInterval(interval);
  }, [modalOpacity]);

  // const toggleModalOff = (): void => {
  //   setModalShow(false);
  // }

  const displayErrors = (): Array<string> => {
    return errors?.length > 0
        ?
          <div className="modal-body error-modal">
            {
              errors.map((error, index) => (
                  <li className="error-modal-list" key={index}>{error}</li>
              ))
            }
          </div>
        :
        null
  }
  if (modalShow === true) {
    return (
      // <div key={errorNumber} style={{ paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%` }} className="modal-show modal"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      //   <div className="modal-dialog" role="document">
      //     <div className="modal-content">
      //         <div className="modal-header">
      //           <h5 className="modal-title title">{title}</h5>
      //           <button onClick={toggleModalOff} className="close" type="button" data-dismiss="modal" aria-label="Close">
      //               <span aria-hidden="true">×</span>
      //           </button>
      //         </div>
        <React.Fragment>
          <BaseModal
                keyValue={errorNumber}
                title={title}
                content={displayErrors()}
                styles={`paddingRight: '17px', display: 'block', opacity:${modalOpacity}%`}
                modalOpacity={modalOpacity}
          />
          {/*{displayErrors()}*/}
              {/*{*/}
              {/*  errors?.length > 0*/}
              {/*    ?*/}
              {/*      <div className="modal-body error-modal">*/}
              {/*        {*/}
              {/*          errors.map((error, index) => (*/}
              {/*              <li className="error-modal-list" key={index}>{error}</li>*/}
              {/*          ))*/}
              {/*        }*/}
              {/*      </div>*/}
              {/*    :*/}
              {/*    null*/}
              {/*  }*/}
        </React.Fragment>
      //     </div>
      //   </div>
      // </div>
    );
  }
}
