import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useRef, useState } from 'react';
import { QueryKey, useQueryClient } from 'react-query';
import { useCreateBenefitMutation } from '@/Features/Benefit/Hooks/useCreateBenefitMutation';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { Form } from '@/Features/Benefit/Components/Form';
import { BenefitFormData } from '@/Features/Benefit/Types';

type Props = {
  queryKey: QueryKey;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const CreateBenefitDrawer: FC<Props> = ({
  queryKey,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const createBenefitMutation = useCreateBenefitMutation();
  const firstInput = useRef(null);
  const queryClient = useQueryClient();
  const benefitQueryKeys = useQueryKeys().benefits;
  const benefitIndexQueryKey = benefitQueryKeys.all;

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleSubmit = (benefit: BenefitFormData) => {
    createBenefitMutation.mutate(benefit, {
      onSuccess: () => {
        queryClient.invalidateQueries(queryKey);
        queryClient.invalidateQueries(benefitIndexQueryKey);
        handleClose();
      },
      onError: (error) => {
        isLaravelValidationError(error) &&
          setErrors(error?.response?.data?.errors);
      },
    });
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Create Store Benefit</Text>
        <Form
          id="benefit-form"
          errors={errors}
          onSubmit={handleSubmit}
          initialFocusRef={firstInput}
        />
        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            type="submit"
            isLoading={createBenefitMutation.isLoading}
            form="benefit-form"
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
