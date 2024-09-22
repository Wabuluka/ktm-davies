import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { ComponentProps, useId, useState } from 'react';
import { useCreateExternalLinkMutation } from '../Hooks/useCreateExternalLinkMutation';
import { ExternalLinkFormData } from '../Types';
import { ExternalLinkForm } from './ExternalLinkForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onStoreSuccess: (externalLink: ExternalLinkFormData) => void;
};

export function CreateExternalLinkDrawer({
  isOpen,
  onClose,
  onStoreSuccess,
}: Props) {
  const formId = useId();
  const [errors, setErrors] = useState<
    ComponentProps<typeof ExternalLinkForm>['errors']
  >({});
  const mutation = useCreateExternalLinkMutation();
  function handleSubmit(formData: ExternalLinkFormData) {
    mutation.mutate(formData, {
      onSuccess: () => {
        onStoreSuccess(formData);
        setErrors(undefined);
      },
      onError: (error) => {
        isLaravelValidationError(error) &&
          setErrors(error?.response?.data?.errors);
      },
    });
  }
  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Create External Link</Text>
      <ExternalLinkForm id={formId} errors={errors} onSubmit={handleSubmit} />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton form={formId} type="submit">
          Add
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
