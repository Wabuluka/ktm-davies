import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef, useState } from 'react';
import { useDeleteCreationTypeMutation } from '../Hooks/useDeleteCreationTypeMutation';
import { useEditCreationTypeMutation } from '../Hooks/useEditCreationTypeMutation';
import { CreationType, CreationTypeFormData } from '../Types';
import { CreationTypeForm } from './CreationTypeForm';

type Props = {
  creationType: CreationType;
  isOpen: boolean;
  onClose: () => void;
};

export function EditCreationTypeDrawer({
  creationType,
  isOpen,
  onClose,
}: Props) {
  const formId = useId();
  const firstInput = useRef(null);
  const editMutation = useEditCreationTypeMutation();
  const deleteMutation = useDeleteCreationTypeMutation();
  const [errors, setErrors] = useState<Record<string, string[]>>();

  function handleClose() {
    setErrors(undefined);
    onClose();
  }
  function handleDeleteButtonClick() {
    if (window.confirm('本当に削除しますか？')) {
      deleteMutation.mutate(creationType.name, {
        onSuccess: handleClose,
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  }
  function handleSubmit(formData: CreationTypeFormData) {
    editMutation.mutate(
      { ...formData, currentName: creationType.name },
      { onSuccess: handleClose },
    );
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
      <Text>Edit Creation Type</Text>
      <CreationTypeForm
        id={formId}
        creationType={creationType}
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
