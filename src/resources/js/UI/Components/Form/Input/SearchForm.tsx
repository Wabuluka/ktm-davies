import { ChevronDownIcon, ChevronUpIcon } from '@chakra-ui/icons';
import {
  Box,
  Collapse,
  IconButton,
  VStack,
  useDisclosure,
} from '@chakra-ui/react';
import React, { FC } from 'react';
import { PrimaryButton } from '../Button/PrimaryButton';

type Props = {
  onSubmit: () => void;
  children: React.ReactNode;
  collapsible?: boolean;
};

export const SearchForm = ({
  onSubmit,
  children,
  collapsible = false,
}: Props) => {
  return collapsible ? (
    <CollapsibleSearchForm>
      <ChildrenWithSubmitButton onSubmit={onSubmit}>
        {children}
      </ChildrenWithSubmitButton>
    </CollapsibleSearchForm>
  ) : (
    <ChildrenWithSubmitButton onSubmit={onSubmit}>
      {children}
    </ChildrenWithSubmitButton>
  );
};

type CollapsibleSearchFormProps = {
  children: React.ReactNode;
};

const CollapsibleSearchForm: FC<CollapsibleSearchFormProps> = ({
  children,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();

  return (
    <>
      <Collapse startingHeight="4.8rem" in={isOpen}>
        {children}
      </Collapse>

      <VStack align="stretch" pos="relative">
        {!isOpen && (
          <Box
            w="100%"
            h="2.8rem"
            backdropFilter="auto"
            backdropBlur="2px"
            pos="absolute"
            bottom="100%"
          />
        )}

        {isOpen ? (
          <IconButton
            as={ChevronUpIcon}
            aria-label="Close Search Field"
            onClick={onClose}
            w="100%"
          />
        ) : (
          <IconButton
            as={ChevronDownIcon}
            aria-label="Open Search Field"
            onClick={onOpen}
            w="100%"
          />
        )}
      </VStack>
    </>
  );
};

type ChildrenWithSubmitButtonProps = {
  onSubmit: () => void;
  children: React.ReactNode;
};

const ChildrenWithSubmitButton = ({
  onSubmit,
  children,
}: ChildrenWithSubmitButtonProps) => {
  return (
    <VStack
      align="stretch"
      bg="gray.50"
      borderRadius="xl"
      borderWidth={2}
      borderColor="gray.200"
      boxShadow="xs"
      p={4}
      gap={4}
      sx={{
        'input, select': { bg: 'white' },
      }}
    >
      {children}

      <PrimaryButton onClick={onSubmit}>Search</PrimaryButton>
    </VStack>
  );
};
