import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useState } from 'react';
import { useBookCreationDispatcher } from '../Hooks/useBookCreationDispatcher';
import { CreationFormData } from '../Types';
import { CreationForm } from './CreationForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export function AddCreationDrawer({ isOpen, onClose }: Props) {
  const formId = useId();
  const { addCreation } = useBookCreationDispatcher();
  const [submittable, setSubmittable] = useState(false);
  function handleSubmit(newCreation: CreationFormData) {
    addCreation(newCreation);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Edit Author</Text>
      <CreationForm
        id={formId}
        onSubmit={handleSubmit}
        onValid={() => setSubmittable(true)}
        onInvalid={() => setSubmittable(false)}
      />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId} isDisabled={!submittable}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
