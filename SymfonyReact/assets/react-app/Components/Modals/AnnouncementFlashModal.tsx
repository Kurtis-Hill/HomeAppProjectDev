import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from "./BaseModal";

export function AnnouncementFlashModal(props: { title: string; errors: string[]; errorNumber: number; timer: number; }) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  const errorNumber: number = props.errorNumber
  const timer: number = props.timer;

  const [modalOpacity, setModalOpacity] = useState<number>(100);
  const [modalShow, setModalShow] = useState<boolean>(true);

  useEffect(() => {
    const interval = setInterval(() => {
      if (modalOpacity !== 0 && modalShow === true) {
        setModalOpacity(modalOpacity - 1);
      } else {
        // setModalOpacity(100);
        setModalShow(false);
        clearInterval(interval)
      }
    }, timer);

    return () => clearInterval(interval);
  }, [modalOpacity]);


  const displayErrors = (): string|null => {
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

  return (
      <React.Fragment>
        <BaseModal
              keyValue={errorNumber}
              title={title}
              content={displayErrors()}
              modalOpacity={modalOpacity}
              modalShow={modalShow}
              setShowModal={setModalShow}
              label={"Error announcement"}
        />
      </React.Fragment>
  );
}
