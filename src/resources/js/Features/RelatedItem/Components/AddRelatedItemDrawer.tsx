import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef, useState } from 'react';
import { RelatedItemFormData } from '../Types';
import { RelatedItemForm } from './RelatedItemForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (relatedItem: RelatedItemFormData) => void;
};

export function AddRelatedItemDrawer({ isOpen, onClose, onSubmit }: Props) {
  const formId = useId();
  const [value, setValue] = useState(initialValue);
  const submitting = useRef(false);
  const submittable = !!value.relatable_id && !submitting.current;
  function handleClose() {
    setValue(initialValue);
    onClose();
  }
  function handleSubmit() {
    if (submitting.current) {
      return;
    }
    submitting.current = true;
    onSubmit(value);
    handleClose();
    setTimeout(() => {
      submitting.current = false;
    }, 500);
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Create Related Item Information</Text>
      <RelatedItemForm
        id={formId}
        value={value}
        onChange={setValue}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton form={formId} type="submit" isDisabled={!submittable}>
          Add
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}

const initialValue: RelatedItemFormData = {
  relatable_id: '',
  relatable_type: 'book',
  description: '',
};
