import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useState } from 'react';
import { useBookCreationDispatcher } from '../Hooks/useBookCreationDispatcher';
import { CreationFormData } from '../Types';
import { CreationForm } from './CreationForm';

type Props = {
  creation: CreationFormData;
  isOpen: boolean;
  onClose: () => void;
};

export function EditCreationDrawer({ creation, isOpen, onClose }: Props) {
  const formId = useId();
  const { updateCreation } = useBookCreationDispatcher();
  const [submittable, setSubmittable] = useState(true);
  function handleSubmit(updated: CreationFormData) {
    updateCreation(updated, creation.creator_id);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>作家を編集</Text>
      <CreationForm
        id={formId}
        creation={creation}
        onSubmit={handleSubmit}
        onValid={() => setSubmittable(true)}
        onInvalid={() => setSubmittable(false)}
      />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId} isDisabled={!submittable}>
          保存
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
