import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { ComponentProps, useId, useState } from 'react';
import { useQueryClient } from 'react-query';
import { useDeleteExternalLinkMutation } from '../Hooks/useDeleteExternalLinkMutation';
import { useEditExternalLinkMutation } from '../Hooks/useEditExternalLinkMutation';
import { ExternalLink, ExternalLinkFormData } from '../Types';
import { ExternalLinkForm } from './ExternalLinkForm';

type Props = {
  externalLink: ExternalLink;
  isOpen: boolean;
  onClose: () => void;
  onDeleteSuccess: (externalLink: ExternalLink) => void;
  onUpdateSuccess: (externalLink: ExternalLinkFormData) => void;
};

export function EditExternalLinkDrawer({
  externalLink,
  isOpen,
  onClose,
  onDeleteSuccess,
  onUpdateSuccess,
}: Props) {
  const formId = useId();
  const queryClient = useQueryClient();
  const queryKeys = useQueryKeys().externalLinks;
  const editMutation = useEditExternalLinkMutation();
  const deleteMutation = useDeleteExternalLinkMutation();
  const [errors, setErrors] = useState<
    ComponentProps<typeof ExternalLinkForm>['errors']
  >({});
  function handleDeleteButtonClick() {
    window.confirm(`Are you sure to delete${externalLink.title}?`) &&
      deleteMutation.mutate(externalLink.id, {
        onSuccess: () => onDeleteSuccess(externalLink),
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
  }
  function handleSubmit(formData: ExternalLinkFormData) {
    editMutation.mutate(
      { id: externalLink.id, ...formData },
      {
        onSuccess: () => {
          onUpdateSuccess(formData);
          setErrors(undefined);
          queryClient.invalidateQueries(queryKeys.all);
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  }
  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Edit External Link</Text>
      <ExternalLinkForm
        id={formId}
        errors={errors}
        externalLink={externalLink}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <DangerButton onClick={handleDeleteButtonClick}>Delete</DangerButton>
        <PrimaryButton form={formId} type="submit">
          Update
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
