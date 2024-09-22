import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useState, useRef } from 'react';
import { Benefit, BenefitFormData } from '@/Features/Benefit';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { useShowBenefitQuery } from '@/Features/Benefit/Hooks/useShowBenefitQuery';
import { useEditBenefitMutation } from '@/Features/Benefit/Hooks/useEditBenefitMutation';
import { useQueryClient } from 'react-query';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { useDeleteBenefitMutation } from '@/Features/Benefit/Hooks/useDeleteBenefitMutation';
import { Form } from '@/Features/Benefit/Components/Form';

type Props = {
  benefit: Benefit;
  onBenefitDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditBenefitDrawer: FC<Props> = ({
  benefit,
  onBenefitDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();

  const [errors, setErrors] = useState<Record<string, string[]>>();

  const editBenefitMutation = useEditBenefitMutation();
  const deleteBenefitMutation = useDeleteBenefitMutation();

  const { id, name } = benefit;

  const benefitQueryKeys = useQueryKeys().benefits;
  const benefitIndexQueryKey = benefitQueryKeys.all;
  const benefitShowQueryKey = benefitQueryKeys.show(id);

  const { isLoading, isError } = useShowBenefitQuery(id, {
    enabled: isOpen,
  });

  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!benefit) return;

    if (window.confirm(`Are you sure to delete ${name}?`)) {
      deleteBenefitMutation.mutate(id, {
        onSuccess: () => {
          queryClient.invalidateQueries(benefitIndexQueryKey);
          queryClient.removeQueries(benefitShowQueryKey);
          onBenefitDeleted(id);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  const handleSubmit = (formData: BenefitFormData) => {
    editBenefitMutation.mutate(
      { id, ...formData },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(benefitIndexQueryKey);
          queryClient.invalidateQueries(benefitShowQueryKey);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  };

  const firstInput = useRef(null);

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Edit store benefit</Text>

        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !benefit ? (
          <DataFetchError />
        ) : (
          <Form
            id="benefit-form"
            benefit={benefit}
            errors={errors}
            onSubmit={handleSubmit}
            initialFocusRef={firstInput}
          />
        )}

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <DangerButton
            onClick={handleDelete}
            isLoading={deleteBenefitMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton
            type="submit"
            form="benefit-form"
            isDisabled={isLoading}
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
