import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useState } from 'react';
import { RelatedItemFormData } from '../Types';
import { RelatedItemForm } from './RelatedItemForm';

type Props = {
  relatedItem: RelatedItemFormData;
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (relatedItem: RelatedItemFormData) => void;
};

export function EditRelatedItemDrawer({
  relatedItem,
  isOpen,
  onClose,
  onSubmit,
}: Props) {
  const formId = useId();
  const [value, setValue] = useState(relatedItem);
  const submittable = !!value.relatable_id;
  function handleClose() {
    setValue(relatedItem);
    onClose();
  }
  function handleSubmit() {
    if (submittable) {
      onSubmit(value);
      handleClose();
    }
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Edit a Related Item</Text>
      <RelatedItemForm
        id={formId}
        value={value}
        onChange={setValue}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton form={formId} type="submit" isDisabled={!submittable}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
