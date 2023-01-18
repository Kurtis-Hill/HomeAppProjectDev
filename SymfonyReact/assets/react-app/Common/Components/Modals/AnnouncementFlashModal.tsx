import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from "./BaseModal";

export function AnnouncementFlashModal(props: AnnouncementFlashModalInterface) {
  const title: string = props.title
  const dataToList: Array<string> = props.errors
  const dataNumber: number = props.errorNumber
  const timer: number = props.timer;
  const announcementModals: Array<typeof AnnouncementFlashModal> = props.announcementModals ?? null;
  const setAnnouncementModals = props.setAnnouncementModals ?? null;


  const [modalOpacity, setModalOpacity] = useState<number>(100);
  const [modalShow, setModalShow] = useState<boolean>(true);

  useEffect(() => {
    // console.log('ann modal', props.announcementModals)
    const interval = setInterval(() => {
      if (modalOpacity !== 0 && modalShow === true) {
        setModalOpacity(modalOpacity - 1);
      } else {
        console.log('count', props.announcementModals)
        setModalShow(false);
        clearInterval(interval)
        // console.log('data number', dataNumber)
        // if (dataNumber !== null) {

        //   setAnnouncementModals(announcementModals.filter((ann) => {
        //     // console.log('hi its me', key); 
        //     return ann.dataNumber !== dataToList;
        //   }));
        // }
      }
    }, timer);

    return () => clearInterval(interval);
  }, [modalOpacity]);


  const displayErrors = (): string|null => {
    return dataToList?.length > 0
        ?
          <div className="modal-body error-modal">
            {
              dataToList.map((error, index) => (
                  <li className="error-modal-list" key={index}>{error}</li>
              ))
            }
          </div>
        :
        null
  }

  return (
      <React.Fragment>
        <BaseModal
              keyValue={dataNumber}
              title={title}              
              modalOpacity={modalOpacity}
              modalShow={modalShow}
              setShowModal={setModalShow}
              label={"Error announcement"}
              indexPosition={1060}
        >
          { displayErrors() }
        </BaseModal>
      </React.Fragment>
  );
}

export interface AnnouncementFlashModalInterface {
  title: string; 
  errors: string[]; 
  errorNumber: number; 
  timer: number; 
  announcementModals?: Array<typeof AnnouncementFlashModal>;
  setAnnouncementModals?: (announcementModals: Array<typeof AnnouncementFlashModal>) => void;
}


