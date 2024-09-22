import { Benefit } from '@/Features/Benefit';
import { BenefitSelection } from '@/Features/Benefit/Components/BenefitSelection';
import { EditBenefitDrawer } from '@/Features/Benefit/Components/EditBenefitDrawer';
import { SelectBenefitDrawer } from '@/Features/Benefit/Components/SelectBenefitDrawer';
import {
  useBookFormState,
  useSetBookFormData,
} from '@/Features/Book/Context/BookFormContext';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { DeleteIcon } from '@chakra-ui/icons';
import {
  Box,
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
  HStack,
  IconButton,
  Table,
  Tbody,
  Td,
  Text,
  Tr,
  VStack,
} from '@chakra-ui/react';
import { FC, useState } from 'react';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export const BenefitDrawer: FC<Props> = ({ isOpen, onClose }) => {
  const { data } = useBookFormState();
  const initialBenefits = data?.benefits || [];
  const [benefits, setBenefits] = useState<Benefit[]>(initialBenefits);
  const { setData } = useSetBookFormData();

  const handleClose = () => {
    const benefitsData = data?.benefits || [];
    setBenefits(benefitsData);
    onClose();
  };

  const handleSelect = (benefit: Benefit) => {
    setBenefits((prev) =>
      prev?.find((item) => item.id === benefit.id)
        ? prev
        : [...(prev ?? []), benefit],
    );
  };

  const handleUpdate = (benefit: Benefit) => {
    setBenefits(
      (prev) =>
        prev?.map((item) =>
          item.id === benefit.id ? { ...item, ...benefit } : item,
        ) ?? null,
    );
  };

  const handleDelete = (benefitId: number): void => {
    setBenefits((prev) => prev.filter((item) => item.id !== benefitId));
  };
  const handleDeleteByMutation = (benefitId: number): void => {
    handleDelete(benefitId);
    setData((prev) => ({
      ...prev,
      benefits: prev.benefits?.filter((item) => item.id !== benefitId),
    }));
  };

  function handleSubmit() {
    setData('benefits', benefits);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={handleClose}>
      <Text>Add Store Benefit</Text>
      <VStack align="stretch">
        <Box>
          <FormControl isRequired>
            <FormLabel>Store Benefit</FormLabel>
          </FormControl>
          <HStack spacing={8}>
            <Table maxW="60rem" mb={2}>
              <Tbody>
                {benefits.map((benefit, i) => (
                  <Tr key={i}>
                    <Td>
                      <BenefitSelection
                        benefitId={benefit.id}
                        handleUpdate={handleUpdate}
                      />
                    </Td>
                    <Td>
                      <EditBenefitDrawer
                        benefit={benefit}
                        onBenefitDeleted={handleDeleteByMutation}
                        renderOpenDrawerElement={(onOpen) => (
                          <EditButton
                            onClick={onOpen}
                            aria-label="Edit store benefit"
                          />
                        )}
                      />
                    </Td>
                    <Td
                      w={18}
                      sx={{
                        '.chakra-button': {
                          bg: 'red.500',
                          color: 'white',
                          p: 2,
                        },
                      }}
                    >
                      <IconButton
                        as={DeleteIcon}
                        aria-label="Delete Store benefit"
                        onClick={() => handleDelete(benefit.id)}
                      />
                    </Td>
                  </Tr>
                ))}
              </Tbody>
            </Table>
          </HStack>
          <SelectBenefitDrawer
            onSubmit={(benefit: Benefit) => {
              handleSelect(benefit);
            }}
            onBenefitDeleted={handleDeleteByMutation}
            renderOpenDrawerElement={(onOpen) => (
              <PrimaryButton onClick={onOpen} aria-label="Select store benefit">
                Add
              </PrimaryButton>
            )}
          />
        </Box>
      </VStack>
      <ButtonGroup>
        <Button variant="outline" onClick={handleClose}>
          Back
        </Button>
        <PrimaryButton onClick={handleSubmit}>Save</PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
};
