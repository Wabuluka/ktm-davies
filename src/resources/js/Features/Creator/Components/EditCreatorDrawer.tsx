import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef, useState } from 'react';
import { useDeleteCreatorMutation } from '../Hooks/useDeleteCreatorMutation';
import { useEditCreatorMutation } from '../Hooks/useEditCreatorMutation';
import { Creator, CreatorFormData } from '../Types';
import { CreatorForm } from './CreatorForm';

type Props = {
  creator: Creator;
  isOpen: boolean;
  onClose: () => void;
};

export function EditCreatorDrawer({ creator, isOpen, onClose }: Props) {
  const formId = useId();
  const firstInput = useRef(null);
  const editMutation = useEditCreatorMutation();
  const deleteMutation = useDeleteCreatorMutation();
  const [errors, setErrors] = useState<Record<string, string[]>>();

  function handleClose() {
    setErrors(undefined);
    onClose();
  }
  function handleDeleteButtonClick() {
    if (window.confirm('Are you sure to delete?')) {
      deleteMutation.mutate(creator.id, {
        onSuccess: handleClose,
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
      handleClose();
    }
  }
  function handleSubmit(formData: CreatorFormData) {
    editMutation.mutate(
      { ...formData, id: creator.id },
      {
        onSuccess: handleClose,
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
      <Text>Edit Creator</Text>
      <CreatorForm
        id={formId}
        creator={creator}
        errors={errors}
        initialFocusRef={firstInput}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <DangerButton onClick={handleDeleteButtonClick}>Delete</DangerButton>
        <PrimaryButton
          type="submit"
          form={formId}
          isLoading={editMutation.isLoading}
        >
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
